var SMCONFIGS = {
    merchant_url: "https://pwa-commerce.com/",
    simicart_url: "https://dashboard.pwa-commerce.com/appdashboard/rest/app_configs/",
    simicart_authorization: "q7wWTxNrRL0F0k86LYp0UA96HkixDn94vG0odOQ",
    notification_api: "simipwa/index/",
    base_name: "",
    logo_url: "https://www.simicart.com/skin/frontend/default/simicart2.0/images/simicart/new_logo_small.png",
    has_connector: true
};

var DEFAULT_COLORS = {
    key_color: '#ff9800',
    top_menu_icon_color: '#ffffff',
    button_background: '#ff9800',
    button_text_color: '#ffffff',
    menu_background: '#1b1b1b',
    menu_text_color: '#ffffff',
    menu_line_color: '#292929',
    menu_icon_color: '#ffffff',
    search_box_background: '#f3f3f3',
    search_text_color: '#7f7f7f',
    app_background: '#ffffff',
    content_color: '#131313',
    image_border_color: '#f5f5f5',
    line_color: '#e8e8e8',
    price_color: '#ab452f',
    special_price_color: '#ab452f',
    icon_color: '#717171',
    section_color: '#f8f8f9',
    status_bar_background: '#ffffff',
    status_bar_text: '#000000',
    loading_color: '#000000',
};

var DESKTOP_MENU = [
    {
        menu_item_id: 1,
        code: 'menu_trigger',
    },
    {
        menu_item_id: 2,
        title: 'Bottom',
        children: [
            {
                title: 'Bottom',
                link: '/venia-bottoms/venia-pants.html'
            },
            {
                title: 'Skirts',
                link: '/venia-bottoms/venia-skirts.html'
            }
        ],
        image_url: 'https://magento23.pwa-commerce.com/pub/media/catalog/category/softwoods-hardwoods-lp-2.jpg',
        link: '/venia-bottoms.html'
    },
    {
        menu_item_id: 3,
        title: 'Top',
        children: [
            {
                title: 'Blouses & Shirts',
                link: '/venia-tops/venia-sweaters.html'
            },
            {
                title: 'Sweaters',
                link: '/venia-tops/venia-blouses.html'
            }
        ],
        link: '/venia-tops.html'
    },
    {
        menu_item_id: 4,
        title: 'Accessories',
        children: [
            {
                title: 'Sub of accessories',
                children: [
                    {
                        title: 'Jewelry',
                        link: '/venia-accessories/venia-jewelry.html'
                    },
                    {
                        title: 'Scarves',
                        link: '/venia-accessories/venia-scarves.html'
                    },
                ]
            },
            {
                title: 'Belts',
                link: '/venia-accessories/venia-belts.html'
            }
        ],
        link: '/venia-accessories.html'
    }
]