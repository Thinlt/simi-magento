import React, {useState, useEffect} from 'react'
// import defaultStyle from './style.css'
// import classify from 'src/classify';
// import Newsletter from './Newsletter';
import Identify from "src/simi/Helper/Identify";
// import {Link} from 'react-router-dom';
import Copyright from './Copyright';
import Facebook from 'src/simi/App/Bianca/BaseComponents/Icon/Facebook'
import Twitter from 'src/simi/App/Bianca/BaseComponents/Icon/Twitter'
import Instagram from 'src/simi/App/Bianca/BaseComponents/Icon/Instagram'
import Expansion from 'src/simi/App/Bianca/BaseComponents/Expansion'
import classes from './ProxyClasses';
import { Link } from 'src/drivers';
import { logoUrl, logoAlt } from 'src/simi/App/Bianca/Helper/Url';
import Subscriber from './Subscriber';

require('./footer.scss');

const Footer = props => {
    // const {classes} = props;
    const [isPhone, setIsPhone] = useState(window.innerWidth < 1024);
    const $ = window.$;
    const [expanded, setExpanded] = useState(null);
    const pagec1 = 1;
    const pagep2 = 2;

    const contactUs = [
        {
            id: 1,
            link: '#',
            title: Identify.__('+ 44 345 678 9009')
        },
        {
            id: 2,
            link: '#',
            title: Identify.__('Example@gmail.com')
        }
    ]

    const customerServices = [
        {
            id: 1,
            link: "#",
            title: Identify.__("Delivery")
        },
        {
            id: 2,
            link: "#",
            title: Identify.__("Exchange & Returns")
        },
        {
            id: 3,
            link: "#",
            title: Identify.__("Payment Methods")
        },
        {
            id: 4,
            link: "#",
            title: Identify.__("Gift Cards")
        },
        {
            id: 5,
            link: "#",
            title: Identify.__("Live Chat")
        },
        {
            id: 6,
            link: "/faq.html",
            title: Identify.__("FAQ")
        }
    ];

    const information = [
        {
            id: 1,
            link: "/abount-us.html",
            title: Identify.__('About us')
        },
        {
            id: 2,
            link: "#",
            title: Identify.__('Careers')
        },
        {
            id: 3,
            link: "#",
            title: Identify.__('Advertising')
        },
        {
            id: 4,
            link: "#",
            title: Identify.__('Cooperation')
        },
        {
            id: 5,
            link: "#",
            title: Identify.__('Terms and Conditions')
        },
        {
            id: 6,
            link: "/privacy-policy.html",
            title: Identify.__('Privacy Policy')
        },
        {
            id: 7,
            link: "/cookie-policy.html",
            title: Identify.__('Cookie Policy')
        }
    ]

    
    const listPages = pages => {
      
        let result = null;
        if(pages.length > 0) {
            result = pages.map((page, index) => {
                return (
                    <li key={index}>
                        <Link to={page.link}>{page.title}</Link>
                    </li>
                );
            })
        }

        return <ul>{result}</ul>;
    }

    const resizePhone = () => {
        $(window).resize(function() {
            const width = window.innerWidth;
            const newIsPhone = width < 1024
            if(isPhone !== newIsPhone){
                setIsPhone(newIsPhone)
            }
        })
    }

    const handleExpand = (expanded) => {
        setExpanded(expanded);
    }

    useEffect(() => {
        resizePhone();
    })

    return (
        <div className={classes['footer-app'] + (isPhone ? ' on-mobile':'')}>
            <div className={classes['footer-wrapper']}>
                <div className={`container`}>
                    <div className={`row`}>
                        <div className={`col-md-4 col-md-offset-4`}>
                            <div className="footer-logo">
                                <Link to='/'>
                                    <img src={logoUrl()} alt={logoAlt()} />
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div className={`row`}>
                        <div className={`col-md-4 col-md-offset-4`}>
                            <div className="footer-subscriber">
                                <h3>subscribe newsletter</h3>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                <Subscriber />
                            </div>
                        </div>
                    </div>
                </div>
                <div className={`container list-item`}>
                    <div className={`row`}>
                        <div className={`col-md-3`}>
                            {!isPhone ? 
                                <React.Fragment>
                                    <span className={classes["footer--title"]}>
                                        {Identify.__("Contact Us")}
                                    </span>
                                    <ul>
                                        {
                                            contactUs.map((page, index) => {
                                                return <li key={index}>
                                                    <Link to={page.link}>{page.title}</Link>
                                                </li>
                                            })
                                        }
                                        <li>
                                            <div className={classes["social-icon"]}>
                                                <a href='https://www.facebook.com/simicart' target="__blank">
                                                    <Facebook className={classes["facebook-icon"]} />
                                                </a>
                                                <a href='https://www.instagram.com/simicart.official/' target="__blank">
                                                    <Instagram className={classes["instagram-icon"]} />
                                                </a>
                                                <a href='https://twitter.com/SimiCart' target="__blank">
                                                    <Twitter className={classes["twitter-icon"]} />
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </React.Fragment>
                                :
                                <div className={`footer-mobile`}>
                                    <Expansion id={`expan-1`} title={Identify.__("Contact Us")} icon_color="#FFFFFF"
                                        handleExpand={(expanId) => handleExpand(expanId)} expanded={expanded} 
                                        content={listPages(contactUs)}
                                    />
                                </div>
                            }
                        </div>

                        <div className={`col-md-3`}>
                            {!isPhone ? 
                                <React.Fragment>
                                    <span className={classes["footer--title"]}>
                                        {Identify.__("Customer Services")}
                                    </span>
                                    {listPages(customerServices)}
                                </React.Fragment>
                                :
                                <div className={`footer-mobile`}>
                                    <Expansion id={`expan-2`} title={Identify.__("Customer Services")} 
                                        content={listPages(customerServices)} icon_color="#FFFFFF" 
                                        handleExpand={(expanId) => handleExpand(expanId)} expanded={expanded} 
                                    />
                                </div>
                            }
                        </div>

                        <div className={`col-md-3`}>
                            {!isPhone ? 
                                <React.Fragment>
                                    <span className={classes["footer--title"]}>
                                        {Identify.__("Information")}
                                    </span>
                                    {listPages(information)}
                                </React.Fragment>
                                :
                                <div className={`footer-mobile`}>
                                    <Expansion id={`expan-3`} title={Identify.__("Information")} icon_color="#FFFFFF" 
                                        handleExpand={(expanId) => handleExpand(expanId)} expanded={expanded} 
                                        content={listPages(information)}
                                    />
                                </div>
                            }
                        </div>

                        <div className={`col-md-3`}>
                            {!isPhone ? 
                                <React.Fragment>
                                    <span className={classes["footer--title"]}>
                                        {Identify.__("our app")}
                                    </span>
                                    <ul>
                                        <li>
                                            <div className={classes["download-icon"]}>
                                                <div className="google-play"><img src="/images/google-play.png" alt="google-play"/></div>
                                                <div className="app-store"><img src="/images/app-store.png" alt="app-store"/></div>
                                            </div>
                                        </li>
                                    </ul>
                                </React.Fragment>
                            :
                                <div className={`footer-mobile download-app`}>
                                    <Expansion id={`expan-4`} title={Identify.__("Our app")} icon_color="#FFFFFF" 
                                        handleExpand={(expanId) => handleExpand(expanId)} expanded={expanded} 
                                        content={(
                                            <React.Fragment>
                                                <ul>
                                                    <li>
                                                        <div className={classes["download-icon"]}>
                                                            <div className="google-play"><img src="/images/google-play.png" alt="google-play"/></div>
                                                            <div className="app-store"><img src="/images/app-store.png" alt="app-store"/></div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </React.Fragment>
                                        )}
                                    />
                                </div>
                            }
                        </div>
                    </div>
                </div>
            </div>
            <Copyright isPhone={isPhone} classes={classes}/>
        </div>
    )
}
export default Footer