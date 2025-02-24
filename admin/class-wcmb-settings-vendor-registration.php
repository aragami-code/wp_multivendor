<?php

class WCMb_Settings_Vendor_Registration {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmb_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMb;
        ?>
<h4><?php echo __('Setting panel to add extra fields in vendor registration page, along with the','MB-multivendor'); ?> </h4>
        <div id="nav-menus-frame" ng-app="vendor_registration">
            <div id="menu-settings-column" class="metabox-holder" ng-controller="postbox_menu">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox" ng-class="postboxClass">
                        <button ng-click="togglePostbox()" aria-expanded="false" class="handlediv button-link" type="button"><span class="screen-reader-text">Toggle panel: Format</span><span aria-hidden="true" class="toggle-indicator"></span></button>
                        <h3 class="hndl ui-sortable-handle">
                            <span><?php echo __('Form Fields','MB-multivendor'); ?></span>
                        </h3>
                        <div class="inside">
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textbox', 'Text Box', $event)" class="button-secondary"><?php echo __('Textbox','MB-multivendor'); ?></a>
                            </p>
                           
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('email', 'Email', $event)" class="button-secondary"><?php echo __('Email','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('url', 'Url', $event)" class="button-secondary"><?php echo __('Url','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textarea', 'Text Area', $event)" class="button-secondary"><?php echo __('Textarea','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('selectbox', 'Select Box', $event)" class="button-secondary"><?php echo __('List','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('checkbox', 'Checkbox', $event)" class="button-secondary"><?php echo __('Checkbox','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('recaptcha', 'Recaptcha', $event)" class="button-secondary"><?php echo __('Recaptcha','MB-multivendor'); ?></a>
                            </p>    
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('file', 'Attachment', $event)" class="button-secondary"><?php echo __('Attachment','MB-multivendor'); ?></a>
                            </p> 
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('separator', 'Section', $event)" class="button-secondary"><?php echo __('Section','MB-multivendor'); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox" ng-class="vendorStoreFieldClass">
                        <button ng-click="togglevendorStoreField()" aria-expanded="false" class="handlediv button-link" type="button"><span class="screen-reader-text">Toggle panel: Format</span><span aria-hidden="true" class="toggle-indicator"></span></button>
                        <h3 class="hndl ui-sortable-handle">
                            <span><?php echo __('Vendor Store Fields','MB-multivendor'); ?></span>
                        </h3>
                        <div class="inside">
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_description', 'Store Description', $event)" class="button-secondary"><?php echo __('Store Description','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_address_1', 'Address 1', $event)" class="button-secondary"><?php echo __('Address 1','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_address_2', 'Address 2', $event)" class="button-secondary"><?php echo __('Address 2','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_phone', 'Phone', $event)" class="button-secondary"><?php echo __('Phone','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_country', 'Country', $event)" class="button-secondary"><?php echo __('Country','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_state', 'State', $event)" class="button-secondary"><?php echo __('State','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_city', 'City', $event)" class="button-secondary"><?php echo __('City','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_postcode', 'PostCode', $event)" class="button-secondary"><?php echo __('Postcode','MB-multivendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_paypal_email', 'Paypal Email', $event)" class="button-secondary"><?php echo __('PayPal Email','MB-multivendor'); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="poststuff" ng-controller="postbox_content">
                <div id="post-body">
                    <div id="post-body-content">
                        <div id="wcmb-vendor-form">
                            <input type="button" value="Save" ng-click="saveFormData()" class="button-primary menu-save">
                            <a disabled="" ng-show="showSaveSpinner" class="button-secondary" href="#"><span style="visibility: visible; float: left;" class="spinner"></span></a>
                            
                            <div ng-if="fields.length === 0" class="wcmb-form-empty-container"><?php echo __('Build your form here','MB-multivendor'); ?></div>
                            
                            <ul class="meta-box-sortables" ui-sortable="fieldSortableOptions" ng-model="fields">
                                <li ng-repeat="(parentIndex,field) in fields track by $index">
                                    <div class="postbox" ng-class="{'closed' : field.hidden }">
                                        <button aria-expanded="false" ng-click="togglePostboxField($index)" class="handlediv button-link" type="button"><span class="screen-reader-text">Toggle panel: Format</span><span aria-hidden="true" class="toggle-indicator"></span></button>
                                        <h2 class="hndle ui-sortable-handle" ng-dblclick="togglePostboxField($index)"><span>{{field.label}}</span></h2>
                                        <div class="inside">
                                            <div id="post-formats-select">
                                                <div ng-include src="partialUrl+field.partial"></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <input type="button" value="Save" ng-click="saveFormData()" class="button-primary menu-save">
                            <a disabled="" ng-show="showSaveSpinner" class="button-secondary" href="#"><span style="visibility: visible; float: left;" class="spinner"></span></a>
                            <h4><?php printf(__('Use %s, %s, %s CSS class to customize the form','MB-multivendor'), '[wcmb-regi-12]', '[wcmb-regi-6]', '[wcmb-regi-4]'); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
