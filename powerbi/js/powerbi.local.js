/**
 * @file
 */

(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.PowerBiBehavior = {
    attach: function (context) {
      var embedConfiguration = {
        type: drupalSettings.powerbi.display,
        id: drupalSettings.powerbi.powerBI.value[0].id,
        embedUrl: drupalSettings.powerbi.powerBI.value[0].embedUrl,
        //pageName: 'ReportSection',
        settings: {
          navContentPaneEnabled: false
        },
        accessToken: drupalSettings.powerbi.accessToken,
      };

      var $reportContainer = $('#reportContainer');
      if (drupalSettings.powerbi.powerBI.value[0].embedUrl) {
        var report = powerbi.embed($reportContainer.get(0), embedConfiguration);
      }
      else {
        console.log('Embed Url is missing');
      }
      // Enable debugging mode.
      if (drupalSettings.debug) {
        console.log(drupalSettings.powerbi.powerBI.value);
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
