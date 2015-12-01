=== WP Retina 2x ===
Contributors: TigrouMeow
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=H2S7S3G4XMJ6J
Tags: retina, images, image, responsive, admin, attachment, media, files, iphone, ipad, high-dpi
Requires at least: 3.5
Tested up to: 4.4
Stable tag: 4.0.3

Make your website look beautiful and crisp on modern displays by creating and displaying retina images. WP 4.4+ is also supported and enhanced.

== Description ==

This plugin creates the image files required by the High-DPI devices and displays them to your visitors accordingly. Your website will look beautiful and crisp on every device! The retina images will be generated for you automatically - though you can also do it manually - and served to the retina devices.

It supports different methods to serve the images to your visitors, for instance: PictureFill (future HTML standard + its JS polyfill), Retina.js (JS only), IMG Rewrite (on-the-fly HTML rewrite) and Retina-Images (server handler). A lazy-loading option is available as well. Pick the one that works best with your hosting and WordPress environment. Multi-site are also supported.

From version 4.4, WordPress has support for Responsive Images. WP Retina 2x handles this well and nicely adds the retina images in the src-set created by WordPress. The HTML for the images not handled by WP 4.4 will also be handled by the plugin. Moreover, the plugin also has two options specific to WP 4.4+. One is to disable the Responsive Image support, the second one is to disable the additional image size called Medium Large.

The plugin is very fast and optimized. It doesn't create anything in the database. In most cases, it doesn't even require any configuration. More information and tutorial available one http://apps.meow.fr/wp-retina-2x/.

PS: The plugin cannot add retina support to your CSS, and therefore your CSS background images. If your themes are using them heavily, you can contact the theme author and kindly ask him to use WP Retina 2x API to add retina support to those background images (that is possible this way). Please note that a few gallery and slider plugins are using CSS background images as well.

= Quickstart =

1. Set your option (for instance, you probably don't need retina images for every sizes set-up in your WP).
2. Generate the retina images (required only the first time, then images are generated automatically).
3. Check if it works! - if it doesn't, read the FAQ, the tutorial, and check the forums.

== Changelog ==

= 4.0.3 =
* Add: Display the image size name and the retina width x height when hovering the little squares.
* Add: Option to disable the new Medium Large image size brought by WP 4.4.
* Add: Option to disable the automatic handling of responsive image (src-set) by WP 4.4.
* Add: Add the Retina images in the Responsive Image tag created by WP 4.4.
* Update: Retina information has been moved to the Media Library directly.
* Update: Dashboard has been revamped for Pro users. Standard users can still use Bulk functions.
* Update: Support for WP 4.4.
* Info: I have published a new book and it is now available on Amazon! It is called "Abandoned Japan" and features stories and adventures in abandoned places in Japan. The book includes Gunkanjima (the abandoned island seen in James Bond) and Nara Dreamland (an abandoned Disneyland). Please have a look at it here: https://goo.gl/dQJpJW. Thank you :)

= 3.5.4 =
* Update: PictureFill 3.0.1 (full codebase rewrite).

= 3.5.2 =
* Update: Little modification for SmushIt (https://wordpress.org/support/topic/wp-retina-2x-support-in-wp-smush?replies=1#post-7460268).

= 3.5.0 =
* Update: Towards using the new WP translation system.

= 3.4.8 =
* Update: For WordPress 4.3.
* Update: RetinaImages to 1.7.2.

= 3.4.6 =
* Fix: Search string not null but empty induces error.
* Change: User Agent used for Pro authentication.

= 3.4.4 =
* Fix: Issues with class containing trailing spaces. Fixed in in SimpleHTMLDOM.
* Fix: Used to show weird numbers when using 9999 as width or height.
* Add: Filter and default filter to avoid certain IMG SRC to be checked/parsed by the plugin while rendering.

= 3.4.2 =
* Fix: Full-Size Retina wasn't removed when the original file was deleted from WP.

= 3.4.0 =
* Fix: Images set up with a 0x0 size must be skipped.

= 3.3.8 =
* Fix: There was an issue if the class starts with a space (broken HTML), plugin automatically fix it on the fly.
* Fix: Full-Size image had the wrong path in the Details screen.
* Fix: Option Auto Generate was wrongly show unchecked even though it is active by default.
* Update: Moved the filters to allow developers to use files hosted on another server.
* Update: Translation strings. If you want to translate the plugin in your language, please contact me :)

= 3.3.6 =
* Fix: There was an issue with local path for a few installs.
* Add: Introduced $wr2x_extra_debug for extra developer debug (might be handy).

= 3.3.5 =
Fix: Very minor issue (one of the debug line had a bug).

= 3.3.4 =
* Fix: Issues with retina images outside the uploads directory.
* Info: Please write a review for the plugin if you are happy with it. I am trying my best to make this plugin to work with every kind of WP install and system :)

= 3.3.2 =
* Fix: Use WP uploads folder for temporary files to avoid issues depending on hosting services.

= 3.3.1 =
* Update: LazySize from 1.0 to 1.1.
* Update: PictureFill from 2.3.0 to 2.3.1.

= 3.3.0 =
* Fix: Used a PHP shortcut that only works in PHP 5.4. Shortcut removed.
* Fix: Support for BedRock and a few more customized installs.
* Info: If you encounter any issue, please roll-back to 3.2.8 and come to the support forum (https://wordpress.org/support/plugin/wp-retina-2x). If you are happy with it, please write a little review (https://wordpress.org/support/view/plugin-reviews/wp-retina-2x) :) Nice week-end everyone!

= 3.2.9 =
* Fix: Support for BedRock and a few more customized installs.
* Update: Allows a little error margin for the resolution of images being uploaded for full-size retina.

= 3.2.8 =
* Fix: Support for custom uploads directory.
* Info: Added error_log for BedRock related debugging (commented, check line 137 in main file). BedRock users should try to modify the wr2x_get_wordpress_upload_root function (in wp-retina-2x.php) to make it work for them. Let's talk about it on https://wordpress.org/support/topic/path-incorrect-as-custom-uploads-directory-location-with-bedrock.

= 3.2.7 =
* Add: API filters to give the opportunity to other plugins to plug into... this plugin ;)

= 3.2.6 =
* Add: Check the maximum upload value in PHP settings before actually uploading (to avoid silenced crashes).
* Update: PictureFill from 2.2.0 to 2.3.0 (https://github.com/scottjehl/picturefill/releases/tag/2.3.0).

= 3.2.4 =
* Add: Custom CDN Domain support (check the "Custom CDN Domain" option).
* Fix: Removed a console.log that was forgotten ;)
* Change: different way of getting the temporary folder to write files (might help in a few cases).

= 3.2.2 =
* Fix: Drag & drop images wasn't working on Firefox and Safari.
* Info: Please rate the plugin if you love it and never hesitate to post features requests :) Thank you!

= 3.2.0 =
* Fix: There was an issue when re-sizing PNG files.
* Change: Lazysizes from 1.0.0 to 1.0.1 (seo improvement).
* Change: Use minified version of retinajs.

= 3.1.0 =
* Add: Lazy-loading option for PictureFill (Pro).
* Fix: For the Pro users having the IXR_client error.

= 3.0.6 =
* Fix: Plugin now works even behind a proxy.
* Fix: Little UI bug while uploading a new image.

= 3.0.4 =
* Add: In the dashboard, added tooltips showing the sizes of the little squares on hover.
* Fix: The plugin was not compatible with Polylang, now it works.

= 3.0.0 =
* Add: Link to logs from the dashboard (if logs are available), and possibility to clear it directly.
* Add: Replace the Full-Size directly by drag & drop in the box.
* Add: Support for WPML Media.
* Change: Picturefill script to 'v2.2.0 - 2014-02-03'.
* Change: Enhanced logs (in debug mode), much easier to read.
* Change: Dashboard enhanced, more clear, possibility of having many image sizes on the screen.
* Fix: Better handing of non-image media and image detection.
* Fix: Rounding issues always been present, they are now fixed with an 2px error margin.
* Fix: Warnings and issues in case of broken metadata and images.
* Add: (PRO) New pop-up screen with detailed information.
* Add: (PRO) Added Retina for Full-Size with upload feature. Please note that Full-Size Retina also works with the normal version but you will have to manually resize and upload them.
* Add: (PRO) Option to avoid removing img's src when using PictureFill.
* Info: The serial for the Pro version can be bought at http://apps.meow.fr/wp-retina-2x. Thanks for all your support, the plugin is going to be 3 years old this year! :)

= 2.6.0 =
* Add: Support Manual Image Crop, resize the @2x as the user manually cropped them (that's cool!).
* Change: Name will change little by little to WP Retina X and menus simplified to simply "Retina".
* Change: Simplification of the dashboard (more is coming).
* Change: PictureFill updated to 'v2.2.0 - 2014-12-19'.
* Fix: Issue with the upload directory on some installs.
* Info: Way more is coming soon to the dashboard, thanks for your patience :)
* Info: Manual Image Crop received a Pull Request from me to support the Retina cropping but it is not part of their current version yet (1.07). For a version of Manual Image Crop that includes this change, you can use my forked version: https://github.com/tigroumeow/wp-manual-image-crop.

= 2.4.0 =
* Fix: Cropped images from the side now supported.
* Fix: Avoid loading the PHP Simple HTML DOM Parser twice.
* Update: PictureFill, from 2.1.0 to 2.2.0.
* Change: Now create retina files by default.
* Info: If you are using LIGHTROOM, please check my new plugin called WP/LR Sync, you might find it very useful (apps.meow.fr/wplr-sync/). I am also preparing WP Retina 2x for a Pro version. Many improvements are on the way so if you have any request, please let me know here: https://wordpress.org/support/topic/what-about-a-pro-version.

= 2.2.0 =
* Change: Links, documentation, readme.

= 2.0.8 =
* Add: Option to disable Retina in the WP Admin. Actually now disabled by default to avoid an issue with NextGen.
* Add: Option to disable the loading of the PictureFill script.
* Update: PictureFill, from 2.1.0 (2014-08-20) to 2.1.0 (2014-10-07).
* Change: Flattr button doesn't pop anymore. I know, that was annoying ;)
* Info: I am thinking of adding features through a pro version. I would love to know your thoughts. Please check this: https://wordpress.org/support/topic/what-about-a-pro-version

= 2.0.6 =
* Works with WP 4.

= 2.0.4 =
* Fix: PictureFill method now handles special characters.
* Change: Performance boost for PictureFill method.
* Change: Use PHP Simple HTML DOM instead of DOMDocument for PictureFill.
* Update: PictureFill, from 2.1.0 (2014-06-03) to 2.1.0 (2014-08-20).

= 2.0.2 =
* Fix: PictureFill issue with older version of PHP
* Fix: issue with boolean values in the options
* Fix: PictureFill method now ignore fallback img tags found in picture tags
* Change: logging enhanced for PictureFill

= 2.0.0 =
* Info: The new method PictureFill is currently beta but I believe is the best. Please help me test it and participate in the WordPress forums if you find any bug or a way to enhance it. Also, thanks a lot to those who made donations! :)
* Change: new PictureFill method
* Change: texts and method names
* Fix: debug mode was not logging
* Update for WordPress 3.9.1

= 1.9.4 =
* Update: for WordPress 3.9.
* Update: MobileDetect, from 2.6.0 to 2.8.0.
* Update: RetinaJS, from 1.1 to 1.3.
* Info: if you want new features / enhancements, please add a message in the WordPress forum and consider a little donation (or a flattr) and I will do my best to include it in the upcoming 2.0 version of the plugin.

= 1.9.2 =
* Fix: issue with the src-set method.
* Change: thumbnail size was reduced in the Retina dashboard.
* Update: French translation.

= 1.9.0 =
* Fix: issues when using custom UPLOADS / WP_SITEURL constants.
* Info: Please come say hello or make a donation if you love this plugin :)
* Info: I am getting married this year!

= 1.8.0 =
* Fix: HTML5 issues with the HTML srcset method.
* Change: RetinaJS (client-side) was updated to 1.1.0.

= 1.6.2 =
* Fix: encoding issue with the HTML srcset method.

= 1.6.0 =
* Add: HTML srcset method.
* Change: use one file less.
* Change: most methods were renamed nicely.

= 1.4.0 =
* Add: german translation and italian translation.
* Add: option to ignore mobile.
* Fix: avoid warnings if any issues during HTML Rewrite.
* Fix: generate button was not working anymore.
* Change: more logging for debug mode.
* Add: progress % during operations.

= 1.2.0 =
* Add: new method called "HTML Rewrite".
* Change: .htaccess regex for images.
* Add: donation button (can be removed, check the FAQ).
* Change: new icons.
* Add: french translation.
* Fix: little fixes.

= 1.0.0 =
* Change: enhancement of the Retina Dashboard.
* Change: better management of the 'issues'.
* Change: handle images with technical problems.
* Fix: random little fixes again.

= 0.9.8 =
* Change: upload is now HTML5, by drag and drop in the Retina Dashboard!
* Add: delete all retina files button.
* Change: hide the columns to ignore in the Retina dashboard.
* Change: generate button only generates pending items (images).
* Fix: performance boost!
* Fix: random little fixes.

= 0.9.6 =
* Fix: warnings when uploading/replacing an image file.

= 0.9.4 =
* Fix: esthetical issue related to the icons in the Retina dashboard.
* Fix: warnings when uploading/replacing an image file.

= 0.9.2 =
* Change: Media Replace is not used anymore, the code has been embedded in the plugin directly.

= 0.9 =
* Fix: code cleaning.
* Fix: no more notices in case there are weird/unsupported/broken image files.

= 0.8 =
* Fix: Works with WP 3.5.

= 0.4.2 =
* Update: to the new version of Retina.js (client-method).
* Fix: updated rewrite-rule (server-method) that works with multi-site.

= 0.4 =
* Fix: support for Network install (multi-site). Thanks to Jeremy (Retina-Images).

= 0.3.4 =
* Change: Retina.js updated to its last version (should be slighlty faster).
* Change: Retina-Images updated to its last version (now handles 404 error, yay!).
* Fix: using a Retina display, the Retina Dashboard was not looking very nice.
* Fix: the "ignored" media for retina are handled in a better way.
* Change: the FAQ was improved.

= 0.3.0 =
* Fix: was not generating the images properly on multisite WordPress installs.
* Add: warning message if using the server-side method without the pretty permalinks.
* Add: warning message if using the server-side method on a multisite WordPress install.
* Change: the client-method (retina.js) is now used by default.

= 0.2.9 =
* Fix: in a few cases, the retina images were not generated (for no apparent reasons).

= 0.2.8 =
* Fix: the retina image was not being generated if equal to the resolution of the original image.
* Add: optimization and enhancement of the issues management.
* Add: a little counter icon to show the number of issues.
* Add: an 'IGNORE' button to hide issues that should not be.

= 0.2.6 =
* Fix: simplified version of the .htaccess directive.
* Fix: new version of the client-side method (Retina.js), works 100x faster.

= 0.2.4 =
* Fix: SQL optimization & memory usage huge improvement.

= 0.2.2 =
* Fix: the recommended resolution shown wasn't the most adequate one.
* Fix: in a few cases, the .htaccess wasn't properly generated.
* Fix: files were renamed to avoid conflicts.
* Add: paging for the Retina Dashboard.
* Add: 'Generate for all files' handles and shows if there are errors.

= 0.2.1 =
* Removed 'error_reporting' (triggers warnings and notices with other plugins).
* Fix: on uninstall/disable, the .htaccess will be updated properly.

= 0.2 =
* Add: the Retina Dashboard.
* Add: can now generate Retina files in bulk.
* Fix: the cropped images were not 'cropped'.
* Add: The Retina Dashboard and the Media Library's column can be disabled via the settings.
* Fix: resolved more PHP warning and notices.

= 0.1.8 =
* Fix: resolved PHP warnings and notices.

= 0.1.6 =
* Change: simplified the code of the server-side method.

= 0.1.4 =
* Fix: the wrong resolution was displayed in the Retina column of the Media Manager.

= 0.1 =
* Very first release.

== Installation ==

Quick and easy installation:

1. Upload the folder `wp-retina-2x` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Check the settings of WP Retina 2x in the WordPress administration screen.
4. Check the Retina Dashboard.
6. Read the tutorial about the plugin: <a href='http://apps.meow.fr/wp-retina-2x/tutorial/'>WP Retina 2x Tutorial</a>.

== Frequently Asked Questions ==

Users, you will find the FAQ here: http://apps.meow.fr/wp-retina-2x/faq/.

Developers, WP Retina 2x has a little API. Here are a few filters and actions you might want to use.

= Functions =
* wr2x_get_retina_from_url( $url ): return the URL of the retina image (empty string if not found)
* wr2x_get_retina( $syspath ): return the system path of the retina image (null if not found)

= Actions =
* wr2x_retina_file_added: called when a new retina file is created, 1st argument is $attachment_id (of the media) and second is the $retina_filepath
* wr2x_retina_file_removed: called when a new retina file is removed, 1st argument is $attachment_id (of the media) and second is the $retina_filepath

= Filters =
* wr2x_img_url: you can check and potentially override the $wr2x_img_url (normal/original image from the src) that will be used in the srcset for 1x
* wr2x_img_retina_url: you can check and potentially override the $wr2x_img_retina_url (retina image) that will be used in the srcset for 2x
* wr2x_img_src: you can check and potentially override the $wr2x_img_src that will be used in the img's src (only used in Pro version)
* wr2x_validate_src: the img src is passed; return it if it is valid, return null if it should be skipped

== Upgrade Notice ==

None.

== Screenshots ==

1. Retina Dashboard
2. Basic Settings
3. Advanced Settings
