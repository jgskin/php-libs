<?php
namespace Jg\Symfony\Template;

class TemplateErrors
{
  static public function getErrors(\sfForm $form, $title = null)
  {
    if (!$form->hasErrors()) {
      return null;
    }

    return self::buildErrors($form->getErrorSchema(), $form, $title);
  }

  static function getFieldLabel($form, $key)
  {
    if (isset($form[$key])) {
      if (!$label = $form->getWidget($key)->getLabel()) {
        return $form->getWidgetSchema()->getFormFormatter()->generateLabelName($key);
      } 
    }

    return null;
  }

  static protected function buildErrors($errors, $form, $title = null)
  {
    $info = array();

    if ($title !== null) {
      $info['title'] = $title;
    } elseif ($form instanceof \sfFormObject) {
      $info['title'] = (string) $form->getObject();
    }

    foreach ($errors as $key => $error) {
      if ($error instanceof \sfValidatorErrorSchema) {
        try {
          $info['next'][] = self::buildErrors($error, $form->getEmbeddedForm($key));
        } catch (\InvalidArgumentException $e) {
          $title = self::getFieldLabel($form, $key);
          $info['next'][] = self::buildErrors($error, $form, $title ? $title : 'Geral');
        }
      } else {
        $info['errors'][$key]['message'] = $error->getMessage();

        if ($label = self::getFieldLabel($form, $key)) {
          $info['errors'][$key]['label'] = $label;
        } 
      }
    }
    
    if (isset($info['next'])) {
      $new_next = array();
      foreach ($info['next'] as $next) {

        if (isset($next['next']) && count($next) == 1) {
          $new_next = array_merge($new_next, $next['next']);
        } else {
          $new_next[] = $next;
        }
      }

      $info['next'] = $new_next;
    }

    return $info;
  }
}