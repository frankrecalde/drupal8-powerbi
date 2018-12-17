<?php

namespace Drupal\powerbi\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class PowerBiConfigurationForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'powerbi_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'powerbi.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = null)
  {
    $config = $this->config('powerbi.settings');
    $state = \Drupal::state();
    $pbi_url = 'https://powerbi.microsoft.com/en-us/landing/signin/';
    $form["#attributes"]["autocomplete"] = "off";
    $form['settings'] = array(
      '#type' => 'vertical_tabs',
    );
  // Administer setting tab.
    $form['powerbi'] = array(
      '#type' => 'details',
      '#title' => $this->t('Power BI settings'),
      '#group' => 'settings',
      '#description' => t('Information below will be used for authenticating user credentials with <a href="@power-bi">Power BI</a> service in order to embedding PBI data with this site.', array('@power-bi' => $pbi_url)),
    );
    // Site wide setting tab.
    $form['site'] = array(
      '#type' => 'details',
      '#title' => $this->t('Site wide settings'),
      '#group' => 'settings',
      '#description' => t('This setting will be available for non administrative activities.'),
    );
    $form['powerbi']['username'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Username'),
      '#default_value' => $config->get('powerbi.username'),
      '#description' => t('Enter your master account user name, which will be used for getting auth token., example:john.doe@yourdomain.com.'),
      '#required' => true,
    );
    $form['powerbi']['client_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Application ID'),
      '#description' => t('Enter your Power BI registered application ID.'),
      '#default_value' => $config->get('powerbi.client_id'),
      '#required' => true,
    );
    $form['powerbi']['client_secret'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Application Secret'),
      '#description' => t('Enter your Power BI registered application secret.'),
      '#default_value' => $config->get('powerbi.client_secret'),
      '#required' => true,
    );
    $form['powerbi']['group_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Group ID'),
      '#description' => t('Enter your Power BI group ID.'),
      '#default_value' => $config->get('powerbi.group_id'),
      '#required' => true,
    );
    $form['powerbi']['password'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Master account Password'),
      '#default_value' => '',
      '#description' => t('Enter your master account password, saved password will not be shown here. Leave blank to make no changes, use an invalid string to disable if need be.'),
    );
    $form['site']['item-check'] = array(
      '#type' => 'fieldset',
      '#group' => 'site',
      '#title' => $this->t('Debug mode'),
      '#description' => t('Enable debugging mode, development purpose only.'),
    );
    $form['site']['item-check']['debug'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable debug mode'),
      '#default_value' => $config->get('powerbi.debug'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $values = $form_state->getValues();
    $config = $this->config('powerbi.settings');
    $state = \Drupal::state();
    $config->set('powerbi.username', $values['username']);
    $config->set('powerbi.client_id', $values['client_id']);
    $config->set('powerbi.client_secret', $values['client_secret']);
    $config->set('powerbi.group_id', $values['group_id']);
    $config->set('powerbi.debug', $values['debug']);
    $config->save();
    if (!empty($values['password'])) {
      $state->set('powerbi.password', $values['password']);
    }
  }
}
