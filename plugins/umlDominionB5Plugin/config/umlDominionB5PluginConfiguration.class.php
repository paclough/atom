<?php

class umlDominionB5PluginConfiguration extends sfPluginConfiguration
{
    public static $summary = 'Custom UML theme overrides intended for Bootstrap 5 base theme (arDominionB5Plugin)';
    public static $version = '0.1.0';

    public function contextLoadFactories(sfEvent $event)
    {
        $context = $event->getSubject();

        // In debug mode, use runtime LESS (vendor/less.js) so you can iterate quickly.
        if ($context->getConfiguration()->isDebug()) {
            $context->response->addJavaScript('/vendor/less.js', 'last');
            $context->response->addStylesheet('/plugins/umlDominionB5Plugin/css/umlDominionB5.less', 'last', ['rel' => 'stylesheet/less', 'type' => 'text/css', 'media' => 'all']);
        } else {
            // In production, compile umlDominionB5.less -> umlDominionB5.css and serve the CSS.
            $context->response->addStylesheet('/plugins/umlDominionB5Plugin/css/umlDominionB5.css', 'last', ['media' => 'all']);
        }
    }

    public function initialize()
    {
        $this->dispatcher->connect('context.load_factories', [$this, 'contextLoadFactories']);

        // Allow optional template overrides if you later add templates/ to this plugin
        $decoratorDirs = sfConfig::get('sf_decorator_dirs');
        $decoratorDirs[] = $this->rootDir.'/templates';
        sfConfig::set('sf_decorator_dirs', $decoratorDirs);

        // Move this plugin to the top to allow overwriting
        $plugins = $this->configuration->getPlugins();
        if (false !== $key = array_search($this->name, $plugins)) {
            unset($plugins[$key]);
        }
        $this->configuration->setPlugins(array_merge([$this->name], $plugins));
    }
}
