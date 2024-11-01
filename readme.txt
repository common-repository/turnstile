=== Turnstile ===
Contributors: bespokeapp
AuthorURI: https://turnstile.me/
Tags: analytics
Requires at least: 4.6
Tested up to: 5.3
Stable tag: 1.4.0
Requires PHP: 5.6.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The Turnstile plugin allows you to easily track users and gate sections of content behind social login by rendering a "Read More" button.

== Description ==

Leveraging the power of the [Turnstile service](https://turnstile.me), the Turnstile plugin allows you to track users coming to your site and find out a little more about who is visiting. Draw them in with the great content for which you're known, and track engagement using the `turnstile_more` shortcode. The shortcode renders a "Read More" button which asks the user to pass through social login, after which they will be redirected to the full page content.

Even if you don't take advantage of the shortcode, the Turnstile plugin can still bolster your experience by keeping track of anonymous users visiting the site. If they do at any point pass through a Turnstile social login, you'll have access to their history on your site at https://turnstile.me.

Have fun!

== Installation ==

[Walkthrough video](https://www.youtube.com/embed/jVoWhthImxE)

1. Upload the plugin files to the `/wp-content/plugins/turnstile` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Turnstile screen to connect the plugin to your turnstile.me account (an account will be created if you don't already have one)
1. Wrap desired post content in `turnstile_more` shortcode and publish to gate content behind social login


== Frequently Asked Questions ==

= Do I need a Turnstile account to use the plugin? =

Yes, but the connection process described in the Installation section above takes care of that for you.

= What kind of information does the Turnstile Plugin track? =

The plugin tracks user page views and interactions with the Read More button.

= GDPR compliance? =

The Turnstile plugin sends user tracking data to servers at https://turnstile.me and sets the cookies listed below. It is up to you, the site owner, to ensure that usage of Turnstile on your site complies with any applicable local and regional regulations such as [EU General Data Protection Regulation (GDPR)](https://www.eugdpr.org/).
To assist with compliance, the Turnstile plugin respects the setting from the popular [cookie-notice plugin](https://wordpress.org/plugins/cookie-notice/) if it is installed. Conditionally [dequeuing](https://codex.wordpress.org/Function_Reference/wp_dequeue_script) `turnstile_js` is another option for deactivating the plugin for compliance.

Turnstile Cookies:

* tsflw: a persistent cookie to track user across site
* sessionid: session for users known to Turnstile
* guest: id for users unknown to Turnstile
* csrftoken: prevention for cross-site forgery

== Screenshots ==

1. This screen shows the Turnstile "Read More" button on-page.
2. This is the user metrics page on https://turnstile.me

== Changelog ==

= 1.0 =
* Initial release

= 1.1 =
* Refactored plugin and added user data management shortcode `turnstile_settings`

= 1.2 =
* Fixed dependency bug

= 1.3 =
* Uses turnstile client script

= 1.4 =
* Removes embedded cookie banner
