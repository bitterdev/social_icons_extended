Do you know the problem? You have successfully set up a site and would like to include a service in the social icons that is usually not included?

Expand the social icons in concrete5 as you like by defining your own services. Simply upload an SVG graphic and enter a name for the social service. You can add as many services as you like.

A global web font and a CSS file with Pseudo-FontAwesome names will be generated in the background each time you make a change, in order to upgrade the uploaded icons for FontAwesome.

By using the SVG file format, which is vector-based, the icons can be scaled freely just like the standard icons without any decrease in quality.

The following web font formats are generated in the background:

- SVG
- TTF
- EOT
- WOFF
- WOFF2  

By using all common web font formats, all modern browsers -  Desktop as well as mobile devices - are supported.

But enough with technical chinese! The usage is very simple. The extension can be operated by a user without programming experience.

**Installation and use**

After successful installation you will find a new dashboard page called "Extend" below the Dashboard page "System & Settings > Basics > Social Links". This page allows you to manage everything. As soon as you have added new icons, you can use them as usual via the normal dashboard page "Social Links" and then integrate them into the frontend of your site using the core block type "Social Icons".  

**Requirements**

- at least a concrete5 version 8.4.1 is required
  - *Older concrete5 versions have a core bug in terms of logic when adding additional social services. A buggy array union will be used to merge the arrays of standard services and additional services. Therefore, I have released this extension starting with 8.4.1. With a core patch of the file "concrete/src/Shareing/SocialNetwork/ServiceList.php" the extension could be used starting with the concrete5 version 8.0. See more details [here](https://github.com/concrete5/concrete5/commit/e2a0fe1b0bde58c3e37d1ae8cdb6f0bf1e9c3450#diff-8c8fd437f53516139fe7da5fb900ba69).*
- the PHP extension ZipArchive is required
  - *This PHP extension is enabled in most web hosting environments and is also listed as recommended in the concrete5 system preferences. Please check this again before you buy my extension.*
- requires an active internet connection
  - *Required when editing additional icons. In standard operation no internet connection is required.*

**Icon Sources**

The following icon sources include CC-0 or similar licensed icons:

* Themify : https://themify.me/themify-icons  ( [OFL](https://github.com/lykmapipo/themify-icons))
* 105 loops: http://dribbble.com/shots/707117-105-Loops-with-PSD
* Batch: https://github.com/AdamWhitcroft/Batch
* Breeze http://breezi.com/blog/free-minimalist-icon-set/
* Broccolidry: http://dribbble.com/shots/587469-Free-16px-Broccolidryiconsaniconsetitisfullof-icons ([custom](http://licence.visualidiot.com/) licence)
* Chunky Pika Icon Set: http://www.smashingmagazine.com/2012/11/11/dutch-icon-set-smashing-edition/
* Climatic icons http://www.adamwhitcroft.com/climacons/
* Creative Commons Web Fonts https://github.com/cc-icons/cc-icons
* Dispicons http://demo.amitjakhu.com/dripicons/
* Eco Ico http://dribbble.com/shots/665585-Eco-Ico
* Ggame Icons http://game-icons.net/
* Gcons: http://www.greepit.com/open-source-icons-gcons/ (license?)
* Heydings (~60 icons only) http://www.heydonworks.com/article/a-free-icon-web-font
* IconFont: http://ux.etao.com/iconfont/
* Iconic https://github.com/somerandomdude/Iconic (OFL, Icons CC BY-SA)
* Iconify.it http://iconify.it/
* Ionicon http://ionicons.com/ (MIT)
* Jigsoar icons: http://www.jigsoaricons.com/
* Linea: http://linea.io/ (CC BY 4.0 license)
* Map Icons http://map-icons.com/
* Metrize Icons: http://www.alessioatzeni.com/metrize-icons/
* MFizz: http://fizzed.com/oss/font-mfizz (MIT)
* Modern Minimal: http://modern.squintongreen.com/
* OpenWeb: Icons http://pfefferle.github.com/openwebicons/ (OFL)
* Octicons: http://octicons.github.com/ (OFL)
* PulsarJS: http://xperiments.es/blog/en/free-pulsarjs-fontface-iconfont/
* Raphael icons set http://icons.marekventur.de/
* Retro: http://yourneighbours.de/web-design/free-retro-icon-set/
* Siconfont http://segundofdez.github.io/siconfont/ (OFL - CC BY)
* SmashMag Freebie http://www.smashingmagazine.com/2011/12/29/freebie-free-vector-web-icons-91-icons/ ([clarified](http://www.smashingmagazine.com/2012/06/18/freebie-academic-icon-set-10-png-psd-icons/#more-130442) licence as CC BY)
* Socicon http://www.socicon.com/ (OFL)
* Victor Erixon icons http://dribbble.com/shots/928458-80-Shades-of-White-Icons
* Washicons http://lucijanblagonic.github.io/Washicons/
* WebHostingHub Glyphs http://www.webhostinghub.com/glyphs/
* Web Symbols Liga http://www.justbenice.ru/studio/websymbolsliga/ (clarify license)
* Windows Icons: https://github.com/Templarian/WindowsIcons
* WPZoom: http://www.wpzoom.com/wpzoom/new-freebie-wpzoom-developer-icon-set-154-free-icons/
* Zurb https://github.com/zurb/foundation-icons
* https://dribbble.com/shots/814118-Icons-v-2-Free-download
* https://dribbble.com/shots/1563947-Free-Icon-Set
* AppBar: https://github.com/olton/Metro-UI-CSS/
* Google Material Design Icons: https://github.com/google/material-design-icons
* Osmic (OSM Icons): https://github.com/nebulon42/osmic

**Credits**

Even if I didn't have to mention it. A big thanks goes to the developers of the free web service [Fontello](http://fontello.com/) for providing the API interface necessary to create the fonts without having to install any additional software on your server. A highly reliable service that runs in the background and is the core of this extension. A big thank you to the development team!
