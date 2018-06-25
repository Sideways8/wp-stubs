<?php

abstract class AdminSettingsPageTabAbstract
{
    public $key = '';

    public $label = '';

    public $admin_page_slug = '';

    public function render() {
        echo 'Overwrite render()...';
    }

    public function save() {}

    ////////////////////////

    public function __construct() {
        add_filter( 'admin_settings_page_tabs', [$this, '_hook_settings_page_tabs'], 10, 2 );
        add_action( 'admin_settings_page_render_tab', [$this, '_hook_settings_page_render'], 10, 2 );
        add_action( 'admin_settings_page_save_tab', [$this, '_hook_settings_page_save'], 10, 2 );
    }

    public function _hook_settings_page_save( $tab, $slug ) {
        if ( $tab !== $this->key || $slug !== $this->admin_page_slug ) {
            return;
        }
        $this->render();
    }

    public function _hook_settings_page_render( $tab, $slug ) {
        if ( $tab !== $this->key || $slug !== $this->admin_page_slug ) {
            return;
        }
        $this->render();
    }

    public function _hook_settings_page_tabs( $tabs, $slug ) {
        if ( $slug !== $this->admin_page_slug ) {
            return null;
        }
        $tabs[$this->key] = $this->label;
        return $tabs;
    }
}