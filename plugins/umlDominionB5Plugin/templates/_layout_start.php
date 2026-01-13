<!DOCTYPE html>
<html lang="<?php echo $sf_user->getCulture(); ?>" dir="<?php echo sfCultureInfo::getInstance($sf_user->getCulture())->direction; ?>">
  <head>
    <?php echo get_partial('default/googleAnalytics'); ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_title(); ?>
    <?php echo get_component('default', 'tagManager', ['code' => 'script']); ?>
    <link rel="shortcut icon" href="<?php echo public_path('favicon.ico'); ?>">
    <script defer src="/dist/js/vendor.bundle.51cf3c3989802bda6211.js"></script><script defer src="/dist/js/umlDominionB5Plugin.bundle.44d4e6bf71dfa37e8ae7.js"></script><link href="/dist/css/umlDominionB5Plugin.bundle.8d0e1faeac03b0feb79c.css" rel="stylesheet">
    <?php echo get_component_slot('css'); ?>
  </head>
  <body class="d-flex flex-column min-vh-100 <?php echo $sf_context->getModuleName(); ?> <?php echo $sf_context->getActionName(); ?><?php echo sfConfig::get('app_show_tooltips') ? ' show-edit-tooltips' : ''; ?>">
    <?php echo get_component('default', 'tagManager', ['code' => 'noscript']); ?>
    <?php echo get_partial('header'); ?>
    <?php include_slot('pre'); ?>
