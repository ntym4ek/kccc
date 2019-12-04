<?php

function _registration_create($data)
{
    $success = false;
    $message = t('Request was not registered. Try later.');
    if (!empty($data['nid'])) {
        $entity = node_load($data['nid']);
        $entity_type = 'node';
        list($entity_id) = entity_extract_ids($entity_type, $entity);

        if (registration_status($entity_type, $entity_id)) {
            $registration_type = registration_get_entity_registration_type($entity_type, $entity);
            $registration = entity_get_controller('registration')->create(array(
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'type' => $registration_type,
                'state' => 'complete',
            ));

            $registration->field_reg_sure['und'][0]['value'] = $data['reg_sure'];
            $registration->field_date_select['und'][0]['value'] = $data['date_select'];
            $registration->field_company['und'][0]['value'] = $data['company'];
            $registration->field_profile_post['und'][0]['value'] = $data['profile_post'];
            $registration->field_fullname['und'][0]['value'] = $data['fullname'];
            $registration->field_phone['und'][0]['value'] = $data['phone'];
            $registration->field_contact_message['und'][0]['value'] = $data['contact_message'];

            registration_save($registration);
            $success = true;
            $message = '';
        }
    }

    return [
        'success' => $success,
        'message' => $message
    ];
}