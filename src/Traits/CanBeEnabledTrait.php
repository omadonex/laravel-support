<?php

namespace Omadonex\LaravelSupport\Traits;

trait CanBeEnabledTrait
{
    protected function getEnabledFieldName()
    {
        $propFieldName = 'enabledFieldName';
        $fieldName = property_exists($this, $propFieldName) ? $this->$propFieldName : 'enabled';

        return $fieldName;
    }

    public function isEnabled()
    {
        $fieldName = $this->getEnabledFieldName();

        return $this->$fieldName;
    }

    public function setEnabledValue($enabled)
    {
        $fieldName = $this->getEnabledFieldName();
        if ($this->$fieldName !== $enabled) {
            $this->update([
                $fieldName => $enabled,
            ]);

            if ($enabled && method_exists($this, 'enabledPositiveAction')) {
                $this->enabledPositiveAction();
            }

            if (!$enabled && method_exists($this, 'enabledNegativeAction')) {
                $this->enabledNegativeAction();
            }
        }
    }

    public function enable()
    {
        $this->setEnabledValue(true);
    }

    public function disable()
    {
        $this->setEnabledValue(false);
    }

    public function canEnable()
    {
        if (method_exists($this, 'checkEnable')) {
            return $this->checkEnable();
        }

        return !$this->isEnabled();
    }

    public function canDisable()
    {
        if (method_exists($this, 'checkDisable')) {
            return $this->checkDisable();
        }

        return $this->isEnabled();
    }

    public function cantEnableText()
    {
        return trans('support::common.message.cantEnable');
    }

    public function cantDisableText()
    {
        return trans('support::common.message.cantDisable');
    }

    public function scopeEnabled($query)
    {
        $fieldName = $this->getEnabledFieldName();

        return $query->where($fieldName, true);
    }

    public function scopeDisabled($query)
    {
        $fieldName = $this->getEnabledFieldName();

        return $query->where($fieldName, false);
    }

    public function scopeByEnabled($query, $enabled)
    {
        $fieldName = $this->getEnabledFieldName();

        return $query->where($fieldName, $enabled);
    }
}
