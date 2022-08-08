<?php

namespace Nebula\Widgets\Plugins;

use Nebula\Plugin;
use Nebula\Helpers\Renderer;
use Nebula\Helpers\Validate;
use Nebula\Widgets\Database;
use Nebula\Widgets\Notice;

class Handle extends Database
{
    /**
     * 启用插件
     *
     * @return void
     */
    private function enable()
    {
        $pluginName = Method::factory()->getPluginClassName($this->params('pluginName'));

        // 已启用插件列表
        $plugins = Plugin::export();

        // 判断组件是否已启用
        if (array_key_exists($pluginName, $plugins)) {
            Notice::factory()->set('不能重复启用插件', 'warning');
            $this->response->redirect('/admin/plugins.php');
        }

        // 判断插件是否存在异常
        if (!class_exists($pluginName) || !method_exists($pluginName, 'activate')) {
            Notice::factory()->set('无法启用插件', 'warning');
            $this->response->redirect('/admin/plugins.php');
        }

        // 获取插件配置
        $renderer = new Renderer();
        call_user_func([$pluginName, 'config'], $renderer);
        $pluginConfig = $renderer->getValues();

        // 获取插件选项
        call_user_func([$pluginName, 'activate']);
        Plugin::activate($pluginName, $pluginConfig);

        // 提交修改
        $this->db->update('options', ['value' => serialize(Plugin::export())], ['name' => 'plugins']);

        Notice::factory()->set('启用成功', 'success');
        $this->response->redirect('/admin/plugins.php');
    }

    /**
     * 禁用插件
     *
     * @return void
     */
    private function disabled()
    {
        // 插件类名
        $pluginClassName = Method::factory()->getPluginClassName($this->params('pluginName'));

        // 已启用插件列表
        $plugins = Plugin::export();

        // 判断组件是否已停用
        if (!isset($plugins[$pluginClassName])) {
            Notice::factory()->set('不能重复停用插件', 'warning');
            $this->response->redirect('/admin/plugins.php');
        }

        // 判断插件是否存在异常
        if (!class_exists($pluginClassName) || !method_exists($pluginClassName, 'activate')) {
            Notice::factory()->set('无法停用插件', 'warning');
            $this->response->redirect('/admin/plugins.php');
        }

        // 获取插件选项
        call_user_func([$pluginClassName, 'deactivate']);
        Plugin::deactivate($pluginClassName);

        // 提交修改
        $this->db->update('options', ['value' => serialize(Plugin::export())], ['name' => 'plugins']);

        Notice::factory()->set('禁用成功', 'success');
        $this->response->redirect('/admin/plugins.php');
    }

    /**
     * 更新插件配置
     *
     * @return void
     */
    private function updateConfig()
    {
        // 已启用插件列表
        $plugins = Plugin::export();

        // 插件类名
        $pluginClassName = Method::factory()->getPluginClassName($this->request->post('pluginName'));

        if (null === $pluginClassName) {
            Notice::factory()->set('插件名不能为空', 'warning');
            $this->response->redirect('/admin/plugins.php');
        }

        if (!array_key_exists($pluginClassName, $plugins)) {
            Notice::factory()->set('插件未启用', 'warning');
            $this->response->redirect('/admin/plugins.php');
        }

        $data = $this->request->post();

        $pluginConfig = $plugins[$pluginClassName]['config'];

        $rules = [];
        foreach (array_keys($pluginConfig) as $value) {
            $rules[$value] = [['type' => 'required', 'message' => '「' . $value . '」不能为空']];
        }
        $validate = new Validate($data, $rules);
        if (!$validate->run()) {
            Notice::factory()->set($validate->result[0]['message'], 'warning');
            $this->response->redirect('/admin/plugin-config.php?name=' . $data['pluginName']);
        }

        // 更新
        Plugin::updateConfig($pluginClassName, $data);

        // 提交修改
        $this->db->update('options', ['value' => serialize(Plugin::export())], ['name' => 'plugins']);

        Notice::factory()->set('修改成功', 'success');
        $this->response->redirect('/admin/plugins.php');
    }

    /**
     * 行动方法
     *
     * @return void
     */
    public function action()
    {
        $action = $this->params('action');

        // 启用插件
        $this->on($action === 'enable')->enable();

        // 禁用插件
        $this->on($action === 'disabled')->disabled();

        // 更新插件配置
        $this->on($action === 'update-config')->updateConfig();
    }
}