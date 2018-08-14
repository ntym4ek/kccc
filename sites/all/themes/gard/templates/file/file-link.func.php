<?php

/**
 * Implements theme_file_link().
 */
function gard_file_link($variables) {
    $file = $variables['file'];

    $file_extension = drupal_strtoupper(pathinfo($file->filename, PATHINFO_EXTENSION));
    $file_description = !empty($file->description) ? $file->description : str_replace('.' .$file_extension, '', $file->filename);
    $icon_directory = $variables['icon_directory'];
    //$icon_directory = drupal_get_path('theme', 'opie') . 'images/file-icons';

    $url = file_create_url($file->uri);

    // Human-readable names, for use as text-alternatives to icons.
    $mime_name = array(
        'application/msword' => t('Microsoft Office document icon'),
        'application/vnd.ms-excel' => t('Office spreadsheet icon'),
        'application/vnd.ms-powerpoint' => t('Office presentation icon'),
        'application/pdf' => t('PDF icon'),
        'video/quicktime' => t('Movie icon'),
        'audio/mpeg' => t('Audio icon'),
        'audio/wav' => t('Audio icon'),
        'image/jpeg' => t('Image icon'),
        'image/png' => t('Image icon'),
        'image/gif' => t('Image icon'),
        'application/zip' => t('Package icon'),
        'text/html' => t('HTML icon'),
        'text/plain' => t('Plain text icon'),
        'application/octet-stream' => t('Binary Data'),
    );

    $mimetype = file_get_mimetype($file->uri);
    $icon = theme('file_icon', array(
        'file' => $file,
        'icon_directory' => $icon_directory,
        'alt' => !empty($mime_name[$mimetype]) ? $mime_name[$mimetype] : t('File'),
    ));

    // Set options as per anchor format described at
    // http://microformats.org/wiki/file-format-examples
    $options = array(
        'attributes' => array(
            'type' => $file->filemime . '; length=' . $file->filesize,
            'target' => '_blank',
        ),
    );

    // Use the description as the link text if available.
    if (empty($file->description)) {
        $link_text = $file->filename;
    }
    else {
        $link_text = $file->description;
        $options['attributes']['title'] = check_plain($file->filename);
    }

    $output  = '<div class="file">';
    $output .=  '<div class="file-info">';
    $output .=      l($link_text, $url, $options);
    $output .=      '<span>' . $icon . ' ' . $file_extension . ' - ' . format_size($file->filesize) . '</span>';
    $output .=  '</div>';

    // добавить кнопку на скачивание
    if (!empty($file->display)) {
        $output .= '<div class="file-download"><a href="' . $url . '" class="btn btn-info btn-block" download>' . t('Download') . '</a></div>';
    }
    $output .= '</div>';

    return $output;
}