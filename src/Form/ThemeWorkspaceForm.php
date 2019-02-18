<?php

namespace Drupal\idix_workspace\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form to select the workspace theme.
 *
 * @internal
 */
class ThemeWorkspaceForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'system_themes_workspace_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['workspace.theme'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, array $theme_options = NULL) {

    $form['workspace_theme'] = [
      '#type' => 'details',
      '#title' => $this->t('Workspace theme'),
      '#open' => TRUE,
    ];
    $form['workspace_theme']['workspace_theme'] = [
      '#type' => 'select',
      '#options' => [0 => $this->t('Default theme')] + $theme_options,
      '#title' => $this->t('Workspace theme'),
      '#default_value' => $this->config('system.theme')->get('workspace'),
    ];
    $form['workspace_theme']['actions'] = ['#type' => 'actions'];
    $form['workspace_theme']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    \Drupal::configFactory()->getEditable('system.theme')->set('workspace', $form_state->getValue('workspace_theme'))->save();
  }

}
