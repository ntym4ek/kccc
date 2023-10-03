<?php
/**
 * @file
 * Default theme implementation to format an HTML mail.
 *
 * Copy this file in your default theme folder to create a custom themed mail.
 * Rename it to mimemail-message--[module]--[key].tpl.php to override it for a
 * specific mail.
 *
 * Available variables:
 * - $recipient: The recipient of the message
 * - $subject: The message subject
 * - $body: The message body
 * - $css: Internal style sheets
 * - $module: The sending module
 * - $key: The message identifier
 *
 * @see template_preprocess_mimemail_message()
 */
?>

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body id="mimemail-body" <?php if ($module && $key): print 'class="'. $module .'-'. $key .'"'; endif; ?>>
<table style="font-family: ubuntu,Helvetica,Arial,sans-serif; font-size: 16px; width: 100%;">
  <tr>
    <td align="center">
      <table style="width: 800px; max-width: 800px;">

        <tr>
          <td style="border-bottom: 1px solid #ccc; display: flex; align-items: center; padding: 10px 20px; height: 70px;">
            <img src="<?php print $logo; ?>" style="display: inline-block; max-width: 100%; max-height: 100%;">
            <div style="display: inline-block; font-weight: 500; margin: 0 0 0 1rem; vertical-align: middle;"><?php print $site_name; ?></div>
          </td>
        </tr>

        <tr>
          <td style="border-bottom: 1px solid #ccc; padding: 20px; height:300px; vertical-align: top;">
            <?php print $body; ?>
          </td>
        </tr>

        <tr>
          <td style="font-size: .8em; padding: 10px 20px;">
            <?php print $sign; ?><br />
            <?php if ($notice): ?>
              <span style="color: #bbb;"><?php print $notice; ?></span>
            <?php endif; ?>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>

