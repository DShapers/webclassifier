// Filename: router.js
define([
  'jquery',
  'underscore',
  'backbone',
  'views/WhatsNewView',
  'views/EntitiesView',
  'views/SourcesView',
  'views/FooterView',
], function($, _, Backbone, WhatsNewView, EntitiesView, SourcesView, FooterView) {
  
  var AppRouter = Backbone.Router.extend({
    routes: {
      // Define some URL routes
      'whatsnew': 'showWhatsNew',
      'entities': 'showEntities',
      'sources': 'showSources',
      // Default
      '*actions': 'defaultAction'
    }
  });
  
  var initialize = function() {

    var app_router = new AppRouter;
    
    app_router.on('route:showSources', function(){
        var sourcesView = new SourcesView();
        sourcesView.render();
    });

    app_router.on('route:showWhatsNew', function () {
        var whatsnew = new WhatsNewView();
    });

    app_router.on('route:defaultAction', function (actions) {
        var homeView = new WhatsNewView();
    });

    var footerView = new FooterView();

    Backbone.history.start();
  };

  return { 
    initialize: initialize
  };
});