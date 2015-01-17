<?php

abstract class GitPackageConfigElement{
    /** @var modX $modx */
    protected $modx;
    /** @var GitPackageConfig $config */
    protected $config;
    /** @var string $name */
    protected $name;
    /** @var string $description */
    protected $description = '';
    /** @var string $file */
    protected $file;
    /** @var string $type */
    protected $type;
    /** @var string $extension */
    protected $extension;
    /** @var array $properties */
    protected $properties = array();

    public function __construct(modX &$modx, GitPackageConfig $config) {
        $this->modx =& $modx;
        $this->config = $config;
    }

    public function fromArray($config) {
        if(isset($config['name'])){
            $this->name = $config['name'];
        }else{
            $this->modx->log(MODx::LOG_LEVEL_ERROR, '[GitPackageManagement] Elements: '.$this->type.' - name is not set');
            return false;
        }

        if (isset($config['description'])) {
            $this->description = $config['description'];
        }

        if(isset($config['file'])){
            $this->file = $config['file'];
        } else {
            $this->file = strtolower($this->name).'.'.$this->type . '.' . $this->extension;
        }

        if (isset($config['properties']) && is_array($config['properties'])) {
            $propertiesSet = $this->setProperties($config['properties']);
            if ($propertiesSet === false) return false;
        }

        if ($this->checkFile() == false) {
            return false;
        }

        return true;
    }

    protected function checkFile() {
        $file = $this->config->getPackagePath();
        $file .= '/core/components/'.$this->config->getLowCaseName().'/elements/' . $this->type . 's/' . $this->file;

        if(!file_exists($file)){
            $this->modx->log(MODx::LOG_LEVEL_ERROR, '[GitPackageManagement] Elements: ' . $file . ' - file does not exists');
            return false;
        }

        return true;
    }

    public function getFile() {
        return $this->file;
    }

    public function getName() {
        return $this->name;
    }

    public function getProperties() {
        return $this->properties;
    }

    public function getDescription() {
        return $this->description;
    }

    protected function setProperties($properties) {
        foreach ($properties as $property) {
            $prop = array();

            if (isset($property['name'])) {
                $prop['name'] = $property['name'];
            } else {
                return false;
            }

            if (isset($property['description'])) {
                $prop['desc'] = $property['description'];
            } else {
                $prop['desc'] = $this->config->getLowCaseName() . '.' . strtolower($this->getName()) . '.' . $prop['name'];
            }

            if (isset($property['type'])) {
                $prop['type'] = $property['type'];
            } else {
                $prop['type'] = 'textfield';
            }

            if (isset($property['options'])) {
                $prop['options'] = $property['options'];
            } else {
                $prop['options'] = '';
            }

            if (isset($property['value'])) {
                $prop['value'] = $property['value'];
            } else {
                $prop['value'] = '';
            }

            if (isset($property['lexicon'])) {
                $prop['lexicon'] = $property['lexicon'];
            } else {
                $prop['lexicon'] = $this->config->getLowCaseName() . ':properties';
            }

            if (isset($property['area'])) {
                $prop['area'] = $property['area'];
            } else {
                $prop['area'] = '';
            }

            $this->properties[] = $prop;
        }

        return true;
    }
}