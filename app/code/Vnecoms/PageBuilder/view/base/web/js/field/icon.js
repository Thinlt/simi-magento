define([
    'jquery',
    './abstract',
    'mageUtils',
    'mage/translate'
], function ($, Element, utils, $t) {
    'use strict';

    return Element.extend({
        defaults: {
        	availableIcons: {
        		fa: {
        			title: $t('Font Awesome'),
        			icons: ["fa-500px", "fa-address-book", "fa-address-book-o", "fa-address-card", "fa-address-card-o", "fa-adjust", "fa-adn", "fa-align-center", "fa-align-justify", "fa-align-left", "fa-align-right", "fa-amazon", "fa-ambulance", "fa-american-sign-language-interpreting", "fa-anchor", "fa-android", "fa-angellist", "fa-angle-double-down", "fa-angle-double-left", "fa-angle-double-right", "fa-angle-double-up", "fa-angle-down", "fa-angle-left", "fa-angle-right", "fa-angle-up", "fa-apple", "fa-archive", "fa-area-chart", "fa-arrow-circle-down", "fa-arrow-circle-left", "fa-arrow-circle-o-down", "fa-arrow-circle-o-left", "fa-arrow-circle-o-right", "fa-arrow-circle-o-up", "fa-arrow-circle-right", "fa-arrow-circle-up", "fa-arrow-down", "fa-arrow-left", "fa-arrow-right", "fa-arrow-up", "fa-arrows", "fa-arrows-alt", "fa-arrows-h", "fa-arrows-v", "fa-asl-interpreting", "fa-assistive-listening-systems", "fa-asterisk", "fa-at", "fa-audio-description", "fa-automobile", "fa-backward", "fa-balance-scale", "fa-ban", "fa-bandcamp", "fa-bank", "fa-bar-chart", "fa-bar-chart-o", "fa-barcode", "fa-bars", "fa-bath", "fa-bathtub", "fa-battery", "fa-battery-0", "fa-battery-1", "fa-battery-2", "fa-battery-3", "fa-battery-4", "fa-battery-empty", "fa-battery-full", "fa-battery-half", "fa-battery-quarter", "fa-battery-three-quarters", "fa-bed", "fa-beer", "fa-behance", "fa-behance-square", "fa-bell", "fa-bell-o", "fa-bell-slash", "fa-bell-slash-o", "fa-bicycle", "fa-binoculars", "fa-birthday-cake", "fa-bitbucket", "fa-bitbucket-square", "fa-bitcoin", "fa-black-tie", "fa-blind", "fa-bluetooth", "fa-bluetooth-b", "fa-bold", "fa-bolt", "fa-bomb", "fa-book", "fa-bookmark", "fa-bookmark-o", "fa-braille", "fa-briefcase", "fa-btc", "fa-bug", "fa-building", "fa-building-o", "fa-bullhorn", "fa-bullseye", "fa-bus", "fa-buysellads", "fa-cab", "fa-calculator", "fa-calendar", "fa-calendar-check-o", "fa-calendar-minus-o", "fa-calendar-o", "fa-calendar-plus-o", "fa-calendar-times-o", "fa-camera", "fa-camera-retro", "fa-car", "fa-caret-down", "fa-caret-left", "fa-caret-right", "fa-caret-square-o-down", "fa-caret-square-o-left", "fa-caret-square-o-right", "fa-caret-square-o-up", "fa-caret-up", "fa-cart-arrow-down", "fa-cart-plus", "fa-cc", "fa-cc-amex", "fa-cc-diners-club", "fa-cc-discover", "fa-cc-jcb", "fa-cc-mastercard", "fa-cc-paypal", "fa-cc-stripe", "fa-cc-visa", "fa-certificate", "fa-chain", "fa-chain-broken", "fa-check", "fa-check-circle", "fa-check-circle-o", "fa-check-square", "fa-check-square-o", "fa-chevron-circle-down", "fa-chevron-circle-left", "fa-chevron-circle-right", "fa-chevron-circle-up", "fa-chevron-down", "fa-chevron-left", "fa-chevron-right", "fa-chevron-up", "fa-child", "fa-chrome", "fa-circle", "fa-circle-o", "fa-circle-o-notch", "fa-circle-thin", "fa-clipboard", "fa-clock-o", "fa-clone", "fa-close", "fa-cloud", "fa-cloud-download", "fa-cloud-upload", "fa-cny", "fa-code", "fa-code-fork", "fa-codepen", "fa-codiepie", "fa-coffee", "fa-cog", "fa-cogs", "fa-columns", "fa-comment", "fa-comment-o", "fa-commenting", "fa-commenting-o", "fa-comments", "fa-comments-o", "fa-compass", "fa-compress", "fa-connectdevelop", "fa-contao", "fa-copy", "fa-copyright", "fa-creative-commons", "fa-credit-card", "fa-credit-card-alt", "fa-crop", "fa-crosshairs", "fa-css3", "fa-cube", "fa-cubes", "fa-cut", "fa-cutlery", "fa-dashboard", "fa-dashcube", "fa-database", "fa-deaf", "fa-deafness", "fa-dedent", "fa-delicious", "fa-desktop", "fa-deviantart", "fa-diamond", "fa-digg", "fa-dollar", "fa-dot-circle-o", "fa-download", "fa-dribbble", "fa-drivers-license", "fa-drivers-license-o", "fa-dropbox", "fa-drupal", "fa-edge", "fa-edit", "fa-eercast", "fa-eject", "fa-ellipsis-h", "fa-ellipsis-v", "fa-empire", "fa-envelope", "fa-envelope-o", "fa-envelope-open", "fa-envelope-open-o", "fa-envelope-square", "fa-envira", "fa-eraser", "fa-etsy", "fa-eur", "fa-euro", "fa-exchange", "fa-exclamation", "fa-exclamation-circle", "fa-exclamation-triangle", "fa-expand", "fa-expeditedssl", "fa-external-link", "fa-external-link-square", "fa-eye", "fa-eye-slash", "fa-eyedropper", "fa-fa", "fa-facebook", "fa-facebook-f", "fa-facebook-official", "fa-facebook-square", "fa-fast-backward", "fa-fast-forward", "fa-fax", "fa-feed", "fa-female", "fa-fighter-jet", "fa-file", "fa-file-archive-o", "fa-file-audio-o", "fa-file-code-o", "fa-file-excel-o", "fa-file-image-o", "fa-file-movie-o", "fa-file-o", "fa-file-pdf-o", "fa-file-photo-o", "fa-file-picture-o", "fa-file-powerpoint-o", "fa-file-sound-o", "fa-file-text", "fa-file-text-o", "fa-file-video-o", "fa-file-word-o", "fa-file-zip-o", "fa-files-o", "fa-film", "fa-filter", "fa-fire", "fa-fire-extinguisher", "fa-firefox", "fa-first-order", "fa-flag", "fa-flag-checkered", "fa-flag-o", "fa-flash", "fa-flask", "fa-flickr", "fa-floppy-o", "fa-folder", "fa-folder-o", "fa-folder-open", "fa-folder-open-o", "fa-font", "fa-font-awesome", "fa-fonticons", "fa-fort-awesome", "fa-forumbee", "fa-forward", "fa-foursquare", "fa-free-code-camp", "fa-frown-o", "fa-futbol-o", "fa-gamepad", "fa-gavel", "fa-gbp", "fa-ge", "fa-gear", "fa-gears", "fa-genderless", "fa-get-pocket", "fa-gg", "fa-gg-circle", "fa-gift", "fa-git", "fa-git-square", "fa-github", "fa-github-alt", "fa-github-square", "fa-gitlab", "fa-gittip", "fa-glass", "fa-glide", "fa-glide-g", "fa-globe", "fa-google", "fa-google-plus", "fa-google-plus-circle", "fa-google-plus-official", "fa-google-plus-square", "fa-google-wallet", "fa-graduation-cap", "fa-gratipay", "fa-grav", "fa-group", "fa-h-square", "fa-hacker-news", "fa-hand-grab-o", "fa-hand-lizard-o", "fa-hand-o-down", "fa-hand-o-left", "fa-hand-o-right", "fa-hand-o-up", "fa-hand-paper-o", "fa-hand-peace-o", "fa-hand-pointer-o", "fa-hand-rock-o", "fa-hand-scissors-o", "fa-hand-spock-o", "fa-hand-stop-o", "fa-handshake-o", "fa-hard-of-hearing", "fa-hashtag", "fa-hdd-o", "fa-header", "fa-headphones", "fa-heart", "fa-heart-o", "fa-heartbeat", "fa-history", "fa-home", "fa-hospital-o", "fa-hotel", "fa-hourglass", "fa-hourglass-1", "fa-hourglass-2", "fa-hourglass-3", "fa-hourglass-end", "fa-hourglass-half", "fa-hourglass-o", "fa-hourglass-start", "fa-houzz", "fa-html5", "fa-i-cursor", "fa-id-badge", "fa-id-card", "fa-id-card-o", "fa-ils", "fa-image", "fa-imdb", "fa-inbox", "fa-indent", "fa-industry", "fa-info", "fa-info-circle", "fa-inr", "fa-instagram", "fa-institution", "fa-internet-explorer", "fa-intersex", "fa-ioxhost", "fa-italic", "fa-joomla", "fa-jpy", "fa-jsfiddle", "fa-key", "fa-keyboard-o", "fa-krw", "fa-language", "fa-laptop", "fa-lastfm", "fa-lastfm-square", "fa-leaf", "fa-leanpub", "fa-legal", "fa-lemon-o", "fa-level-down", "fa-level-up", "fa-life-bouy", "fa-life-buoy", "fa-life-ring", "fa-life-saver", "fa-lightbulb-o", "fa-line-chart", "fa-link", "fa-linkedin", "fa-linkedin-square", "fa-linode", "fa-linux", "fa-list", "fa-list-alt", "fa-list-ol", "fa-list-ul", "fa-location-arrow", "fa-lock", "fa-long-arrow-down", "fa-long-arrow-left", "fa-long-arrow-right", "fa-long-arrow-up", "fa-low-vision", "fa-magic", "fa-magnet", "fa-mail-forward", "fa-mail-reply", "fa-mail-reply-all", "fa-male", "fa-map", "fa-map-marker", "fa-map-o", "fa-map-pin", "fa-map-signs", "fa-mars", "fa-mars-double", "fa-mars-stroke", "fa-mars-stroke-h", "fa-mars-stroke-v", "fa-maxcdn", "fa-meanpath", "fa-medium", "fa-medkit", "fa-meetup", "fa-meh-o", "fa-mercury", "fa-microchip", "fa-microphone", "fa-microphone-slash", "fa-minus", "fa-minus-circle", "fa-minus-square", "fa-minus-square-o", "fa-mixcloud", "fa-mobile", "fa-mobile-phone", "fa-modx", "fa-money", "fa-moon-o", "fa-mortar-board", "fa-motorcycle", "fa-mouse-pointer", "fa-music", "fa-navicon", "fa-neuter", "fa-newspaper-o", "fa-object-group", "fa-object-ungroup", "fa-odnoklassniki", "fa-odnoklassniki-square", "fa-opencart", "fa-openid", "fa-opera", "fa-optin-monster", "fa-outdent", "fa-pagelines", "fa-paint-brush", "fa-paper-plane", "fa-paper-plane-o", "fa-paperclip", "fa-paragraph", "fa-paste", "fa-pause", "fa-pause-circle", "fa-pause-circle-o", "fa-paw", "fa-paypal", "fa-pencil", "fa-pencil-square", "fa-pencil-square-o", "fa-percent", "fa-phone", "fa-phone-square", "fa-photo", "fa-picture-o", "fa-pie-chart", "fa-pied-piper", "fa-pied-piper-alt", "fa-pied-piper-pp", "fa-pinterest", "fa-pinterest-p", "fa-pinterest-square", "fa-plane", "fa-play", "fa-play-circle", "fa-play-circle-o", "fa-plug", "fa-plus", "fa-plus-circle", "fa-plus-square", "fa-plus-square-o", "fa-podcast", "fa-power-off", "fa-print", "fa-product-hunt", "fa-puzzle-piece", "fa-qq", "fa-qrcode", "fa-question", "fa-question-circle", "fa-question-circle-o", "fa-quora", "fa-quote-left", "fa-quote-right", "fa-ra", "fa-random", "fa-ravelry", "fa-rebel", "fa-recycle", "fa-reddit", "fa-reddit-alien", "fa-reddit-square", "fa-refresh", "fa-registered", "fa-remove", "fa-renren", "fa-reorder", "fa-repeat", "fa-reply", "fa-reply-all", "fa-resistance", "fa-retweet", "fa-rmb", "fa-road", "fa-rocket", "fa-rotate-left", "fa-rotate-right", "fa-rouble", "fa-rss", "fa-rss-square", "fa-rub", "fa-ruble", "fa-rupee", "fa-s15", "fa-safari", "fa-save", "fa-scissors", "fa-scribd", "fa-search", "fa-search-minus", "fa-search-plus", "fa-sellsy", "fa-send", "fa-send-o", "fa-server", "fa-share", "fa-share-alt", "fa-share-alt-square", "fa-share-square", "fa-share-square-o", "fa-shekel", "fa-sheqel", "fa-shield", "fa-ship", "fa-shirtsinbulk", "fa-shopping-bag", "fa-shopping-basket", "fa-shopping-cart", "fa-shower", "fa-sign-in", "fa-sign-language", "fa-sign-out", "fa-signal", "fa-signing", "fa-simplybuilt", "fa-sitemap", "fa-skyatlas", "fa-skype", "fa-slack", "fa-sliders", "fa-slideshare", "fa-smile-o", "fa-snapchat", "fa-snapchat-ghost", "fa-snapchat-square", "fa-snowflake-o", "fa-soccer-ball-o", "fa-sort", "fa-sort-alpha-asc", "fa-sort-alpha-desc", "fa-sort-amount-asc", "fa-sort-amount-desc", "fa-sort-asc", "fa-sort-desc", "fa-sort-down", "fa-sort-numeric-asc", "fa-sort-numeric-desc", "fa-sort-up", "fa-soundcloud", "fa-space-shuttle", "fa-spinner", "fa-spoon", "fa-spotify", "fa-square", "fa-square-o", "fa-stack-exchange", "fa-stack-overflow", "fa-star", "fa-star-half", "fa-star-half-empty", "fa-star-half-full", "fa-star-half-o", "fa-star-o", "fa-steam", "fa-steam-square", "fa-step-backward", "fa-step-forward", "fa-stethoscope", "fa-sticky-note", "fa-sticky-note-o", "fa-stop", "fa-stop-circle", "fa-stop-circle-o", "fa-street-view", "fa-strikethrough", "fa-stumbleupon", "fa-stumbleupon-circle", "fa-subscript", "fa-subway", "fa-suitcase", "fa-sun-o", "fa-superpowers", "fa-superscript", "fa-support", "fa-table", "fa-tablet", "fa-tachometer", "fa-tag", "fa-tags", "fa-tasks", "fa-taxi", "fa-telegram", "fa-television", "fa-tencent-weibo", "fa-terminal", "fa-text-height", "fa-text-width", "fa-th", "fa-th-large", "fa-th-list", "fa-themeisle", "fa-thermometer", "fa-thermometer-0", "fa-thermometer-1", "fa-thermometer-2", "fa-thermometer-3", "fa-thermometer-4", "fa-thermometer-empty", "fa-thermometer-full", "fa-thermometer-half", "fa-thermometer-quarter", "fa-thermometer-three-quarters", "fa-thumb-tack", "fa-thumbs-down", "fa-thumbs-o-down", "fa-thumbs-o-up", "fa-thumbs-up", "fa-ticket", "fa-times", "fa-times-circle", "fa-times-circle-o", "fa-times-rectangle", "fa-times-rectangle-o", "fa-tint", "fa-toggle-down", "fa-toggle-left", "fa-toggle-off", "fa-toggle-on", "fa-toggle-right", "fa-toggle-up", "fa-trademark", "fa-train", "fa-transgender", "fa-transgender-alt", "fa-trash", "fa-trash-o", "fa-tree", "fa-trello", "fa-tripadvisor", "fa-trophy", "fa-truck", "fa-try", "fa-tty", "fa-tumblr", "fa-tumblr-square", "fa-turkish-lira", "fa-tv", "fa-twitch", "fa-twitter", "fa-twitter-square", "fa-umbrella", "fa-underline", "fa-undo", "fa-universal-access", "fa-university", "fa-unlink", "fa-unlock", "fa-unlock-alt", "fa-unsorted", "fa-upload", "fa-usb", "fa-usd", "fa-user", "fa-user-circle", "fa-user-circle-o", "fa-user-md", "fa-user-o", "fa-user-plus", "fa-user-secret", "fa-user-times", "fa-users", "fa-vcard", "fa-vcard-o", "fa-venus", "fa-venus-double", "fa-venus-mars", "fa-viacoin", "fa-viadeo", "fa-viadeo-square", "fa-video-camera", "fa-vimeo", "fa-vimeo-square", "fa-vine", "fa-vk", "fa-volume-control-phone", "fa-volume-down", "fa-volume-off", "fa-volume-up", "fa-warning", "fa-wechat", "fa-weibo", "fa-weixin", "fa-whatsapp", "fa-wheelchair", "fa-wheelchair-alt", "fa-wifi", "fa-wikipedia-w", "fa-window-close", "fa-window-close-o", "fa-window-maximize", "fa-window-minimize", "fa-window-restore", "fa-windows", "fa-won", "fa-wordpress", "fa-wpbeginner", "fa-wpexplorer", "fa-wpforms", "fa-wrench", "fa-xing", "fa-xing-square", "fa-y-combinator", "fa-y-combinator-square", "fa-yahoo", "fa-yc", "fa-yc-square", "fa-yelp", "fa-yen", "fa-yoast", "fa-youtube", "fa-youtube-play", "fa-youtube-square"],
        		},
        		etl: {
        			title: $t('Font Et-Line'),
        			icons: ["etl-mobile", "etl-laptop", "etl-desktop", "etl-tablet", "etl-phone", "etl-document", "etl-documents", "etl-search", "etl-clipboard", "etl-newspaper", "etl-notebook", "etl-book-open", "etl-browser", "etl-calendar", "etl-presentation", "etl-picture", "etl-pictures", "etl-video", "etl-camera", "etl-printer", "etl-toolbox", "etl-briefcase", "etl-wallet", "etl-gift", "etl-bargraph", "etl-grid", "etl-expand", "etl-focus", "etl-edit", "etl-adjustments", "etl-ribbon", "etl-hourglass", "etl-lock", "etl-megaphone", "etl-shield", "etl-trophy", "etl-flag", "etl-map", "etl-puzzle", "etl-basket", "etl-envelope", "etl-streetsign", "etl-telescope", "etl-gears", "etl-key", "etl-paperclip", "etl-attachment", "etl-pricetags", "etl-lightbulb", "etl-layers", "etl-pencil", "etl-tools", "etl-tools-2", "etl-scissors", "etl-paintbrush", "etl-magnifying-glass", "etl-circle-compass", "etl-linegraph", "etl-mic", "etl-strategy", "etl-beaker", "etl-caution", "etl-recycle", "etl-anchor", "etl-profile-male", "etl-profile-female", "etl-bike", "etl-wine", "etl-hotairballoon", "etl-globe", "etl-genius", "etl-map-pin", "etl-dial", "etl-chat", "etl-heart", "etl-cloud", "etl-upload", "etl-download", "etl-target", "etl-hazardous", "etl-piechart", "etl-speedometer", "etl-global", "etl-compass", "etl-lifesaver", "etl-clock", "etl-aperture", "etl-quote", "etl-scope", "etl-alarmclock", "etl-refresh", "etl-happy", "etl-sad", "etl-facebook", "etl-twitter", "etl-googleplus", "etl-rss", "etl-tumblr", "etl-linkedin", "etl-dribbble"]
        		},
        		capt: {
        			title: $t('Captain Icon'),
        			icons: ["capt-001", "capt-002", "capt-003", "capt-004", "capt-005", "capt-006", "capt-007", "capt-008", "capt-009", "capt-010", "capt-011", "capt-012", "capt-013", "capt-014", "capt-015", "capt-016", "capt-017", "capt-018", "capt-019", "capt-020", "capt-021", "capt-022", "capt-023", "capt-024", "capt-025", "capt-026", "capt-027", "capt-028", "capt-029", "capt-030", "capt-031", "capt-032", "capt-033", "capt-034", "capt-035", "capt-036", "capt-037", "capt-038", "capt-039", "capt-040", "capt-041", "capt-042", "capt-043", "capt-044", "capt-045", "capt-046", "capt-047", "capt-048", "capt-049", "capt-050", "capt-051", "capt-052", "capt-053", "capt-054", "capt-055", "capt-056", "capt-057", "capt-058", "capt-059", "capt-060", "capt-061", "capt-062", "capt-063", "capt-064", "capt-065", "capt-066", "capt-067", "capt-068", "capt-069", "capt-070", "capt-071", "capt-072", "capt-073", "capt-074", "capt-075", "capt-076", "capt-077", "capt-078", "capt-079", "capt-080", "capt-081", "capt-082", "capt-083", "capt-084", "capt-085", "capt-086", "capt-087", "capt-088", "capt-089", "capt-090", "capt-091", "capt-092", "capt-093", "capt-094", "capt-095", "capt-096", "capt-097", "capt-98", "capt-099", "capt-100", "capt-101", "capt-102", "capt-103", "capt-104", "capt-105", "capt-106", "capt-107", "capt-108", "capt-109", "capt-110", "capt-111", "capt-112", "capt-113", "capt-114", "capt-115", "capt-116", "capt-117", "capt-118", "capt-119", "capt-120", "capt-121", "capt-122", "capt-123", "capt-124", "capt-125", "capt-126", "capt-127", "capt-128", "capt-129", "capt-130", "capt-131", "capt-132", "capt-133", "capt-134", "capt-135", "capt-136", "capt-137", "capt-138", "capt-139", "capt-140", "capt-141", "capt-142", "capt-143", "capt-144", "capt-145", "capt-146", "capt-147", "capt-148", "capt-149", "capt-150", "capt-151", "capt-152", "capt-153", "capt-154", "capt-155", "capt-156", "capt-157", "capt-158", "capt-159", "capt-160", "capt-161", "capt-162", "capt-163", "capt-164", "capt-165", "capt-166", "capt-167", "capt-168", "capt-169", "capt-170", "capt-171", "capt-172", "capt-173", "capt-174", "capt-175", "capt-176", "capt-177", "capt-178", "capt-179", "capt-180", "capt-181", "capt-182", "capt-183", "capt-184", "capt-185", "capt-186", "capt-187", "capt-188", "capt-189", "capt-190", "capt-191", "capt-192", "capt-193", "capt-194", "capt-195", "capt-196", "capt-197", "capt-198", "capt-199", "capt-200", "capt-201", "capt-202", "capt-203", "capt-204", "capt-205", "capt-206", "capt-207", "capt-208", "capt-209", "capt-210", "capt-211", "capt-212", "capt-213", "capt-214", "capt-215", "capt-216", "capt-217", "capt-218", "capt-219", "capt-220", "capt-221", "capt-222", "capt-223", "capt-224", "capt-225", "capt-226", "capt-227", "capt-228", "capt-229", "capt-230", "capt-231", "capt-232", "capt-233", "capt-234", "capt-235", "capt-236", "capt-237", "capt-238", "capt-239", "capt-240", "capt-241", "capt-242", "capt-243", "capt-244", "capt-245", "capt-246", "capt-247", "capt-248", "capt-249", "capt-250", "capt-251", "capt-252", "capt-253", "capt-254", "capt-255", "capt-256", "capt-257", "capt-258", "capt-259", "capt-260", "capt-261", "capt-262", "capt-263", "capt-264", "capt-265", "capt-266", "capt-267", "capt-268", "capt-269", "capt-270", "capt-271", "capt-272", "capt-273", "capt-274", "capt-275", "capt-276", "capt-277", "capt-278", "capt-279", "capt-280", "capt-281", "capt-282", "capt-283", "capt-284", "capt-285", "capt-286", "capt-287", "capt-288", "capt-289", "capt-290", "capt-291", "capt-292", "capt-293", "capt-294", "capt-295", "capt-296", "capt-297", "capt-298", "capt-299", "capt-300", "capt-301", "capt-302", "capt-303", "capt-304", "capt-305", "capt-306", "capt-307", "capt-308", "capt-309", "capt-310", "capt-311", "capt-312", "capt-313", "capt-314", "capt-315", "capt-316", "capt-317", "capt-318", "capt-319", "capt-320", "capt-321", "capt-322", "capt-323", "capt-324", "capt-325", "capt-326", "capt-327", "capt-328", "capt-329", "capt-330", "capt-331", "capt-332", "capt-333", "capt-334", "capt-335", "capt-336", "capt-337", "capt-338", "capt-339", "capt-340", "capt-341", "capt-342", "capt-343", "capt-344", "capt-345", "capt-346", "capt-347", "capt-348", "capt-349", "capt-350", "capt-351", "capt-352", "capt-353", "capt-354", "capt-355", "capt-356", "capt-357", "capt-358", "capt-359", "capt-360", "capt-361", "capt-362", "capt-363", "capt-364", "capt-365", "capt-366", "capt-367", "capt-368", "capt-369", "capt-370", "capt-371", "capt-372", "capt-373", "capt-374", "capt-375"]
        		}
        	},
        	icon: '',
        	showIconChooser: '0',
        	fontSize: 15,
        	color: '',
        	colorHover: '',
        	minFontSize: 12,
        	maxFontSize: 50,
        	fontSizeStep: 1,
        	showColor: false,
        	showColorHover: false,
        	showFontSizeChanger: false,
        	currentFont: 'fa',
        	currentFilterFont: 'fa',
        	defaultShowingIconLimit: 135,
        	showingIconsLimit: 135,
    		iconPageSize: 63,
            tracks: {
            	icon: true,
            	fontSize: true,
            	color: true,
            	colorHover: true
            },
            listens: {
            	currentFont: 'elementChanged',
            	icon: 'elementChanged',
            	fontSize: 'elementChanged',
            	color: 'elementChanged',
            	colorHover: 'elementChanged'
            }
        },
        
        /**
         * Get current icon class
         */
        getIconClasses: function (){
        	return this.currentFont()+ ' '+ this.icon();
        },
        
        /**
         * Get available Icons
         */
        getAvailableIcons: function(fontType){
        	var self = this;
        	var count = 0;
        	var result = [];
        	this.availableIcons[fontType].icons.each(function(icon){
        		if(count++ >= self.showingIconsLimit()) return false;
        		result.push(icon);
        	});
        	
        	return result;
        },
        
        /**
         * Bind scroll event to image content
         */
        bindScrollEvent: function(){
        	var self = this;
        	$('#'+this.getFieldId()+'_iconchooser .vpb-icons-list').scroll(function(event){
        		if(!self.canShowMoreIcons()) return;
        		
        		var height = $('#'+self.getFieldId()+'_iconchooser .vpb-icons-container').height() - 20;
        		if($(this).scrollTop() + $(this).height() >= height){
        			self.showMoreIcons();
        		}
        	});
        },
        
        /**
         * Can show more icons button
         */
        canShowMoreIcons: function(){
        	return this.showingIconsLimit() < this.availableIcons[this.currentFilterFont()].icons.size();
        },
        
        
        /**
         * Show more images
         */
        showMoreIcons: function(){
        	this.showingIconsLimit(this.showingIconsLimit()+ this.iconPageSize);
        },
        
        /**
         * Curent filter font
         */
        chooseFont: function(font){
        	this.currentFilterFont(font.code);
        	this.showingIconsLimit(this.defaultShowingIconLimit);
        },
        
        /**
         * is currently filter font
         */
        isCurrentFilterFont: function(font){
        	return this.currentFilterFont() == font.code;
        },
        
        /**
         * Get All fonts
         * @returns
         */
        getFonts: function(){
        	var fonts = [];
        	
        	for(var index in this.availableIcons){
        		fonts.push({
        			code: index,
        			title:this.availableIcons[index].title
    			});
        	}
        	return fonts;
        },
        /**
         * Get value of the field
         */
        getValue: function(){
        	return this.icon;
        },
        
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['icon', 'showIconChooser', 'fontSize', 'color', 'colorHover', 'currentFont', 'currentFilterFont', 'showingIconsLimit']);
            return this;
        },
        
        /**
         * Open icon chooser
         */
        openIconChooser: function(){
        	this.showIconChooser(true);
        },
        
        /**
         * Close icon chooser
         */
        closeIconChooser: function(){
        	this.showIconChooser(false);
        },
        
        /**
         * Set icon class
         */
        setIcon: function(iconClass){
        	this.icon(iconClass);
        	this.currentFont(this.currentFilterFont());
        	this.closeIconChooser();
        },
        
        getFontSizes: function(){
        	var result = [];
        	for(var i = parseInt(this.minFontSize); i <= parseInt(this.maxFontSize); i += parseInt(this.fontSizeStep)){
        		result.push(i);
        	}
        	return result;
        },
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	return {
        		/*type: this.id,
        		position: this.displayArea,*/
        		is_active: this.isActive(),
        		data:{
        			icon: this.icon(),
        			currentFont: this.currentFont(),
        			color: this.color(),
        			colorHover: this.colorHover,
        			fontSize: this.fontSize()
        		}
    		};
        }
    });
});
