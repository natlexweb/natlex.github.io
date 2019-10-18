let app = angular.module('testApp', ['ngMaterial']);
app.controller('testAppController', [function() {

  this.user = {};
  this.messages = [];

  this.countries = ('Select a country Russia Belarus Ukraine').split(/(?=[A-Z])/).map(function(state) {
    return {abbrev: state.slice(0, 3), name: state};
  });

  this.jobs = [{name: 'frontend dev', abbrev: 'frontend dev'}, {name: 'backend dev', abbrev: 'bd'},
               {name: 'software tester', abbrev: 'software tester'}, {name: 'embedded dev', abbrev: 'embedded dev'}];

  this.clearForm = function() {
    const email = this.user.email;
    this.user = {};
    this.user.email = email;
  };

  this.apply = function(valid) {
    this.page = 2;
    if (!valid) {
      console.error("ERROR!!!Put 567733245 in your report =)");
    }
  };

  this.count = function(a ,b) {
    alert((a / b).toFixed(2).toString().replace('.', ','));
  };

  this.addComment = function(message) {
    this.messages.push(message);
  };

  this.getMessages = function() {
	fetch('google.com/@@@@@@').then(function(response) {
   		alert(response.headers.get('Content-Type'));
    		alert(response.status);
    		return response.json();
   		})
 		 .catch(console.error("ERROR!!!Put 00009999 in your report =)"));
  		};
}]);
