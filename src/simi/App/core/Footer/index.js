import React, {useState, useEffect} from 'react'
import defaultStyle from './style.css'
import classify from 'src/classify';
import Newsletter from './Newsletter';
import Identify from "src/simi/Helper/Identify";
import {Link} from 'react-router-dom';
import Copyright from './Copyright';
import Facebook from 'src/simi/BaseComponents/Icon/Facebook'
import Twitter from 'src/simi/BaseComponents/Icon/Twitter'
import Instagram from 'src/simi/BaseComponents/Icon/Instagram'
import Expansion from 'src/simi/BaseComponents/Expansion'

const Footer = props => {
    const {classes} = props;
    const [isPhone, setIsPhone] = useState(window.innerWidth < 1024);
    const [expanded, setExpanded] = useState(null);
    let pagec1 = 1;
    let pagep2 = 2;
    const pageCustomerServices = [
        {
            id: 1,
            link: "/about-us",
            title: "About"
        },
        {
            id: 2,
            link: "/delivery-return",
            title: "Delivery & returns"
        },
        {
            id: 3,
            link: "/trade-services",
            title: "Trade services"
        },
        {
            id: 4,
            link: "/branch-finder",
            title: "Branch Finder"
        },
        {
            id: 5,
            link: "/contacts",
            title: "Contact us"
        }
    ];

    const pagePolicies = [
        {
            id: 1,
            link: "/terms-conditions",
            title: "Terms & Conditions of supply"
        },
        {
            id: 2,
            link: "/terms-conditions",
            title: "Terms of use"
        },
        {
            id: 3,
            link: "/terms-conditions",
            title: "Privacy & cookie policy"
        }
    ];
    
    const listPages = pages => {
      
        let result = null;
        if(pages.length > 0) {
            result = pages.map((page, index) => {
                return (
                    <li key={index}>
                        <Link to="/">{page.title}</Link>
                    </li>
                );
            })
        }

        return <ul>{result}</ul>;
    }

    const resizePhone = () => {
        window.onresize = function () {
            const width = window.innerWidth;
            const newIsPhone = width < 1024
            if(isPhone !== newIsPhone){
                setIsPhone(newIsPhone)
            }
        }
    }

    const handleExpand = (expanded) => {
        setExpanded(expanded);
    }

    useEffect(() => {
        resizePhone()
    }, [])

    return (
        <div className={classes['footer-app']}>
            {/* <Newsletter classes={classes}/> */}
            <div className={classes['footer-middle']}>
                <div className={`container ${classes['col-mobile-pd-0']}`}>
                    <div className={`row ${classes['app--flex']}`}>
                        <div className={`${classes['col-custom-20pr']} ${classes['col-mobile-pd-0']}`}>
                            {!isPhone ? <React.Fragment>
                                <span className={classes["footer--custom_title"]}>
                                {Identify.__("Customer Services")}
                            </span>
                            {listPages(pageCustomerServices)}
                            </React.Fragment>: <Expansion id={pagec1} title={Identify.__("Customer Services")} content={listPages(pageCustomerServices)} icon_color="#FFFFFF" handleExpand={(pagec1) => handleExpand(pagec1)} expanded={expanded} />}
                        </div>
                        <div className={`${classes['col-custom-20pr']} ${classes['col-mobile-pd-0']}`}>
                        {!isPhone ? <React.Fragment>
                            <span className={classes["footer--custom_title"]}>
                            {Identify.__("Our Policies")}
                        </span>
                        {listPages(pagePolicies)}
                            </React.Fragment>: <Expansion id={pagep2} title={Identify.__("Our Policies")} content={listPages(pagePolicies)} icon_color="#FFFFFF" handleExpand={(pagep2) => handleExpand(pagep2)} expanded={expanded} />}
                        </div>
                        <div className={`${classes['col-custom-20pr']} hidden-xs`} />
                        <div className={`${classes['col-custom-20pr']} hidden-xs`} />
                        <div className={`${classes["col-custom-20pr"]} text-right`}>
                            <span className={classes["footer--custom_title"]}>
                                {Identify.__("Get in touch today on")}
                            </span>
                            <ul className={classes["list-contact"]}>
                                <li>
                                    <a href={`tel:842466517968`}>84 - 24 - 6651 - 7968</a>
                                </li>
                                <li>
                                    <a href={`mailto:Support@simicart.com `}>Support@simicart.com</a>
                                </li>
                            </ul>
                            <span
                                className={classes["footer--custom_title"]}
                                style={{
                                    display: "block",
                                    marginTop: "40px"
                                }}
                            >
                                {Identify.__("Connect")}
                            </span>
                            <div className={classes["social__md-block"]}>
                                <a href='/' target="__blank">
                                    <Facebook className={classes["facebook-icon"]} style={{width: "50px", height: "50px"}} />
                                </a>
                                <a href='/' target="__blank">
                                    <Twitter className={classes["twitter-icon"]} style={{width: "50px", height: "50px"}} />
                                </a>
                                <a href='/' target="__blank">
                                    <Instagram className={classes["instagram-icon"]} style={{width: "50px", height: "50px"}} />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <Copyright isPhone={isPhone} classes={classes}/>
        </div>
    )
}
export default classify(defaultStyle)(Footer)   