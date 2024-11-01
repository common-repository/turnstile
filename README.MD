# Wordpress Turnstile Plugin

## Installation
This plugin should be in `/wp-content/plugins/turnstile` in a wordpress installation  

## Features

### User tracking using a js event
Located in `include/load.php` in `turnstile_theme_enqueue_styles`   
When installed, the plugin will execute an ajax call from every page of the customer website to the turnstile server.  
The goal is to track every user interaction with every pages, even if they are guest users and the customer have not installed any turnstile by himself.  
This works as follows :  
1. When someone is loading a page from the customer website, an ajax call to turnstile.me create a virgin turnstile link (if it doesnt exist yet)  
2. This Turnstile link is triggered by the user (website visitor)   


### [turnstile_more] shortcode for user deanonymisation from an article
Located in `shortcodes.php` in `turnstile_readmore_shortcode()`   
A turnstile shortcode can be used in an article to deanonymize users.  
The customer have to write something like
```
This is my free content. What a nice post ! You should click on the "Read more" button
[turnstile_more]
This is the premium content, it will not be displayed 
[/turnstile_more]
```  
Here is what it's doing when a user creates a turnstile_more shortcode :  
1. It creates a Turnstile in turnstile.me linking to the customer article/post (`include/events.php` in `publish_event_add_turnstile_link_more`)  
2. When the customer is saving/updating his post, the turnstile link can be updated with different social login platforms options (admin/metaboxes.php)  
3. In the customer post on the customer wp frontend, the content of the shortcode is hidden/not loaded.  
4. There is a button "read more" redirecting to the turnstile linked to this article  
5. When clicked, the turnstile read more button redirects user (after social login) to the same url but with a /ref=turnstile.me argument  
6. If this ref argument is here in the GET request, then we just display the content of the turnstile_more shortcode  
7. We delete the ref=turnstile.me argument form the webbrowser url field to avoid people from sharing directly the premium article url.  
8. Later, maybe v2, we will consider creating a key for each of those articles so users can't just fuck arround the url parmas to bypass the authentification mechanism. It's simple but definitely not a priority for massive adoption   

### 

## TODO

### Replace user_key with a proper OAuth

### Add user visualization to plugin


