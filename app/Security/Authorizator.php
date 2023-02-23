<?php

namespace App\Security;

use Nette\Security\IResource;
use Nette\Security\Permission;

class Authorizator extends Permission
{
    /**
     * @param array[] $permission
     */
    public function __construct(array $permission)
    {
        // Pole obsahující již přidané Resources (s kterými můžeme pracovat v rámci rolí)
        $existResources = [];

        // Projedeme všechny role
        foreach ($permission as $role => $resources) {
            $this->addRole($role);

            // Definice, kam která role může
            foreach ($resources as $resource) {
                // Pokud budeme mít presenter s nadefinovanými akcemi, tak je záznam vždy pole
                if (is_array($resource)) {
                    // Máme nadefinované jednotlivé akce v presenteru (role má přístup pouze do nich a nikam jinam)
                    foreach ($resource as $presenter => $actions) {
                        $allowActions = [];

                        foreach ($actions as $action) {
                            $allowActions[] = $action;
                        }

                        //$resourceName = "Admin:{$presenter}";
                        $resourceName = $presenter;

                        // V případě, že nemáme přidaný presenter do resources, přidáme ho (kvůli oprávnění)
                        if (!in_array($presenter, $existResources)) {
                            $existResources[] = $presenter;

                            $this->addResource($resourceName);
                        }

                        // @phpstan-ignore-next-line
                        $this->allow($role, $resourceName, empty($allowActions) ? self::ALL : $allowActions);
                    }
                } else {
                    $resourceName = $resource;

                    // V případě, že nemáme přidaný presenter do resources, přidáme ho (kvůli oprávnění)
                    if (!in_array($resource, $existResources)) {
                        $existResources[] = $resource;

                        $this->addResource($resourceName);
                    }

                    // Pokud není pole, má role přístup do celého presenteru (do všech akcí)
                    $this->allow($role, $resourceName, self::ALL);
                }
            }
        }
    }


    public function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL): bool
    {
        if ($resource !== self::ALL) {
            if ($resource instanceof IResource) {
                $resource = $resource->getResourceId();
            }

            if (!in_array($resource, $this->getResources())) {
                $this->addResource($resource);
            }
        }


        return parent::isAllowed($role, $resource, $privilege);
    }
}
