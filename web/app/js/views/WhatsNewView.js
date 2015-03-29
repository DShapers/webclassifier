define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/whatsNewTemplate.html'
], function($, _, Backbone, whatsNewTemplate){

  var WhatsNewView = Backbone.View.extend({
    el: $("#page"),
    data: {},

    initialize: function() {
    	var that = this;
    	$.ajax({
  			url: "http://172.28.128.3:8080/api/v1/entity/_whatsnew",
  			dataType: "json"
		}).done(function(data) {
			that.data = data;
  			that.render();
		});
    },

    render: function(){
    	$('.menu li').removeClass('active');
    	if (window.location.hash) {
      		$('.menu li a[href="'+window.location.hash+'"]').parent().addClass('active');
      	} else {
      		$('.menu li a[href="#/whatsnew"]').parent().addClass('active');
      	}
    	console.log(this.data);
    	var data = { entities : this.data };
    	var compiledTemplate = _.template(whatsNewTemplate);
    	this.$el.html(compiledTemplate(data));
    }

  });

  return WhatsNewView;
  
});