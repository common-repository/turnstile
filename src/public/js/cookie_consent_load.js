window.addEventListener("load", function(){
window.cookieconsent.initialise({
  "palette": {
    "popup": {
      "background": "#000"
    },
    "button": {
      "background": "#f1d600"
    }
  },
  "type": "opt-in",
  "content": {
    "href": "https://turnstile.me/privacy",
    "dismiss": "No thanks"
  },
  onStatusChange: function(status) {
      if (status === 'allow') {
          window.turnstile.event();
      }
  }
})});

