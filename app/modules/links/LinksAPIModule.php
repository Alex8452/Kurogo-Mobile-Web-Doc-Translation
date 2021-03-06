<?php
/**
  * @package Module
  * @subpackage Links
  */

/**
  * @package Module
  * @subpackage Links
  */
class LinksAPIModule extends APIModule {
    protected $id = 'links';
    protected $vmin = 1;
    protected $vmax = 1;
    protected $linkGroups;

    protected function getLinkGroup($group) {
        if (!$this->linkGroups) {
            $this->linkGroups = $this->getModuleSections('links-groups');
        }

        if (isset($this->linkGroups[$group])) {
            if (!isset($this->linkGroups[$group]['links'])) {
                $this->linkGroups[$group]['links'] = $this->getModuleSections('links-' . $group);
            }

            if (!isset($this->linkGroups[$group]['description'])) {
                $this->linkGroups[$group]['description'] = $this->getModuleVar('description','strings');
            }

            return $this->linkGroups[$group];
        } else {
            throw new Exception("Unable to find link group information for $group");
        }
    }

    public function getLinks() {
        return $this->getModuleSections('links');
    }

    protected function getLinkData() {
        $links = $this->getLinks();

        foreach ($links as &$link) {
            if (isset($link['icon']) && strlen($link['icon'])) {
                $link['img'] = "/modules/{$this->configModule}/images/{$link['icon']}{$this->imageExt}";
            }

            if (isset($link['group']) && strlen($link['group'])) {
                $group = $this->getLinkGroup($link['group']);
                if (!isset($link['title']) && isset($group['title'])) {
                    $link['title'] = $group['title'];
                }
                $link['url'] = $this->buildBreadcrumbURL('group', array('group'=>$link['group']));
            }
        }

        return $links;
    }


       public function initializeForCommand() {

        switch ($this->command) {
            case 'group':
                $group = $this->getLinkGroup($this->getArg('group'));
                if (isset($group['title'])) {
                    $this->setPageTitles($group['title']);
                }
                $response = array();
                $displayType = isset($group['display_type']) ? $group['display_type'] : $this->getModuleVar('display_type');
                $response['links'] = $group['links'];
                $response['displayType'] = $displayType;
                $response['description'] = $group['description'];
                break;

                $this->setResponse($response);
                $this->setResponseVersion(1);
                break;

           case 'index':
                $links = $this->getLinkData();
               $response = array();
                $response['description'] = $this->getModuleVar('description','strings');
                $response['displayType'] =  $this->getModuleVar('display_type');
                $response['links'] =  $links;
                
                $this->setResponse($response);
                $this->setResponseVersion(1);
                break;


            default:
                $this->invalidCommand();
                break;
        }
    }
}

