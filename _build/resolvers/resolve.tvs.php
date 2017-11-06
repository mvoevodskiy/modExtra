<?php


$success = false;
switch (@$options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        /* @var modX $modx */
        $modx = &$object->xpdo;

        $equals = array(
            // Keys: comma-separated TVS list
            // Values: array with templates names
            '' => array(
                '',
            ),
        );

        $templates = array();
        /** @var modTemplate[] $tmpls */
        $tmpls = $modx->getCollection('modTemplate');
        foreach ($tmpls as $template) {
            $templates[$template->get('templatename')] = $template->get('id');
        }

        /** @var string[] $tmpls */
        foreach ($equals as $tvNames => $tmpls) {
            $tvNames = explode(',', $tvNames);
            foreach ($tvNames as $tvName) {
                $tvName = trim($tvName);
                /** @var modtemplateVar $tv */
                if ($tv = $modx->getObject('modTemplateVar', array('name' => $tvName))) {
                    $tvts = array();
                    foreach ($tmpls as $tmpl) {
                        if (!$tvt = $modx->getObject('modTemplateVarTemplate',
                            array('tmplvarid' => $tv->get('id'), 'templateid' => $templates[$tmpl]))
                        ) {
                            $tvt = $modx->newObject('modTemplateVarTemplate');
                            $tvt->set('templateid', $templates[$tmpl]);
                            $tvts[] = $tvt;
                        }
                    }

                    $tv->addMany($tvts, 'TemplateVarTemplates');
                    $tv->save();
                }
            }
        }

        $success = true;
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        $success = true;
        break;
}

return $success;