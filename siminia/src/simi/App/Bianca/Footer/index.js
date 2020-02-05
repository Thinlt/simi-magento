import React, { useState, useEffect } from 'react';
// import defaultStyle from './style.css'
// import classify from 'src/classify';
// import Newsletter from './Newsletter';
import Identify from 'src/simi/Helper/Identify';
// import {Link} from 'react-router-dom';
// import Copyright from './Copyright';
import Expansion from 'src/simi/App/Bianca/BaseComponents/Expansion';
import classes from './ProxyClasses';
import { Link } from 'src/drivers';
// import { logoUrl, logoAlt } from 'src/simi/App/Bianca/Helper/Url';
import { footerLogoUrl, footerLogoAlt } from 'src/simi/App/Bianca/Helper/Url';
import Subscriber from './Subscriber';
import Chats from 'src/simi/App/Bianca/BaseComponents/Chats';
import { smoothScrollToView } from 'src/simi/Helper/Behavior';

require('./footer.scss');

const Footer = (props) => {
	// const {classes} = props;
	const [isPhone, setIsPhone] = useState(window.innerWidth < 1024);
	const $ = window.$;
	const [ expanded, setExpanded ] = useState(null);
	const pagec1 = 1;
	const pagep2 = 2;
    var footer_customer_service = null
    var footer_information = null
    var services = null
    var informations = null
    var footer_phone = null
    var footer_email = null
    var footer_facebook = null
    var footer_instagram = null
    var footer_twitter = null
    var footer_linkedin = null
    var footer_google = null
	var bianca_subcribe_description = null
	var bianca_android_app = null
	var bianca_ios_app = null

    const storeConfig = Identify.getStoreConfig();
    // get contactus config
    if(
        storeConfig &&
        storeConfig.simiStoreConfig &&
		storeConfig.simiStoreConfig.config &&
		storeConfig.simiStoreConfig.config.base
    ){
        footer_phone = storeConfig.simiStoreConfig.config.base.bianca_footer_phone
        footer_email = storeConfig.simiStoreConfig.config.base.bianca_footer_email
        footer_facebook = storeConfig.simiStoreConfig.config.base.bianca_footer_facebook
        footer_instagram = storeConfig.simiStoreConfig.config.base.bianca_footer_instagram
        footer_twitter = storeConfig.simiStoreConfig.config.base.bianca_footer_twitter
        footer_linkedin = storeConfig.simiStoreConfig.config.base.bianca_footer_linkedin
        footer_google = storeConfig.simiStoreConfig.config.base.bianca_footer_google
    }

    // get customer services and link
	if (
        storeConfig &&
		storeConfig.simiStoreConfig &&
		storeConfig.simiStoreConfig.config &&
		storeConfig.simiStoreConfig.config.base &&
		storeConfig.simiStoreConfig.config.base.footer_customer_service
	) {
        footer_customer_service = storeConfig.simiStoreConfig.config.base.footer_customer_service
    }
    if(footer_customer_service){
        const cs = JSON.parse(footer_customer_service)
        services = Object.values(cs)
    }

    const listServices = (services) => {
		let result = null;
		if(services){
            if (services.length > 0) {
                result = services.map((service, index) => {
                    return (
                        <li onClick={scrollTop} key={index}>
                            <Link to={service.service_link}>{Identify.__(service.service_title)}</Link>
                        </li>
                    );
                });
            }
        }

		return <ul>{result}</ul>;
	};

    // more informations and link
	if (
        storeConfig &&
		storeConfig.simiStoreConfig &&
		storeConfig.simiStoreConfig.config &&
		storeConfig.simiStoreConfig.config.base &&
		storeConfig.simiStoreConfig.config.base.footer_information
	) {
        footer_information = storeConfig.simiStoreConfig.config.base.footer_information
	}
	
    if(footer_information){
        const inf = JSON.parse(footer_information)
        informations = Object.values(inf)
	}
	
	const scrollTop = () =>{
		smoothScrollToView($("#id-message"));
	}

    const listInfos = (infos) => {
		let result = null;
		if(infos){
            if (infos.length > 0) {
                result = infos.map((info, index) => {
                    return (
                        <li onClick={scrollTop} key={index}>
                            <Link to={info.information_link}>{Identify.__(info.information_title)}</Link>
                        </li>
                    );
                });
            }
        }

		return <ul>{result}</ul>;
	};

	// get subcribe description
	if(
		storeConfig &&
        storeConfig.simiStoreConfig &&
		storeConfig.simiStoreConfig.config &&
		storeConfig.simiStoreConfig.config.base && 
		storeConfig.simiStoreConfig.config.base.bianca_subcribe_description
	){
		bianca_subcribe_description = storeConfig.simiStoreConfig.config.base.bianca_subcribe_description
	}

	// get link android and ios app
	if(
		storeConfig &&
        storeConfig.simiStoreConfig &&
		storeConfig.simiStoreConfig.config &&
		storeConfig.simiStoreConfig.config.base
	){
		if(storeConfig.simiStoreConfig.config.base.bianca_android_app){
			bianca_android_app = storeConfig.simiStoreConfig.config.base.bianca_android_app
		}
		if(storeConfig.simiStoreConfig.config.base.bianca_ios_app){
			bianca_ios_app = storeConfig.simiStoreConfig.config.base.bianca_ios_app
		}
	}

	const contactUs = [
		{
			id: 1,
			link: '#',
			title: Identify.__(footer_phone)
		},
		{
			id: 2,
			link: '#',
			title: Identify.__(footer_email)
		}
    ];

	const listPages = (pages) => {
		let result = null;
		if (pages.length > 0) {
			result = pages.map((page, index) => {
				return (
					<li key={index} className="contact_us">
						<Link to={page.link}>{page.title}</Link>
					</li>
				);
			});
		}

		return <ul>{result}</ul>;
	};

	const resizePhone = () => {
		$(window).resize(function() {
			const width = window.innerWidth;
			const newIsPhone = width < 1024;
			if (isPhone !== newIsPhone) {
				setIsPhone(newIsPhone);
			}
		});
	};

	const handleExpand = (expanded) => {
		setExpanded(expanded);
	};

	useEffect(() => {
		resizePhone();
	});

	return (
		<React.Fragment>
			<div className={classes['footer-app'] + (isPhone ? ' on-mobile' : '')}>
				<div className={classes['footer-wrapper']}>
					<div className={`container`}>
						<div className={`row`}>
							<div className={`col-md-4 col-md-offset-4`}>
								<div className="footer-logo">
									<Link to="/">
										<img src={footerLogoUrl()} alt={footerLogoAlt()} />
									</Link>
								</div>
							</div>
						</div>
						<div className={`row`}>
							<div className={`col-md-4 col-md-offset-4`}>
								<div className="footer-subscriber">
									<h3>subscribe newsletter</h3>
									<p>{Identify.__(bianca_subcribe_description)}</p>
									<Subscriber />
								</div>
							</div>
						</div>
					</div>
					<div className={`container list-item`}>
						<div className={`row`}>
							<div className={`col-md-3`}>
								{!isPhone ? (
									<React.Fragment>
										<span className={classes['footer--title']}>{Identify.__('Contact Us')}</span>
										<ul>
											{contactUs.map((page, index) => {
												return (
													<li key={index} className="contact_us">
														<Link to={page.link}>{page.title}</Link>
													</li>
												);
											})}
											<li>
												<div className={classes['social-icon']}>
													<a href={footer_facebook} target="__blank">
														<div className={classes['facebook-icon']} ></div>
													</a>
													<a href={footer_instagram} target="__blank">
														<div className={classes['instagram-icon']} ></div>
													</a>
													<a href={footer_twitter} target="__blank">
														<div className={classes['twitter-icon']} ></div>
													</a>
													<a href={footer_linkedin} target="__blank">
														<div className={classes['linkedin-icon']} ></div>
													</a>
													<a href={footer_google} target="__blank">
														<div className={classes['google-icon']} ></div>
													</a>
												</div>
											</li>
										</ul>
									</React.Fragment>
								) : (
									<div className={`footer-mobile`}>
										<Expansion
											id={`expan-1`}
											title={Identify.__('Contact Us')}
											icon_color="#FFFFFF"
											handleExpand={(expanId) => handleExpand(expanId)}
											expanded={expanded}
											content={listPages(contactUs)}
										/>
									</div>
								)}
							</div>

							<div className={`col-md-3`}>
								{!isPhone ? (
									<React.Fragment>
										<span className={classes['footer--title']}>{Identify.__('Customer Services')}</span>
										{listServices(services)}
									</React.Fragment>
								) : (
									<div className={`footer-mobile`}>
										<Expansion
											id={`expan-2`}
											title={Identify.__('Customer Services')}
											content={listServices(services)}
											icon_color="#FFFFFF"
											handleExpand={(expanId) => handleExpand(expanId)}
											expanded={expanded}
										/>
									</div>
								)}
							</div>

							<div className={`col-md-3`}>
								{!isPhone ? (
									<React.Fragment>
										<span className={classes['footer--title']}>{Identify.__('Information')}</span>
										{listInfos(informations)}
									</React.Fragment>
								) : (
									<div className={`footer-mobile`}>
										<Expansion
											id={`expan-3`}
											title={Identify.__('Information')}
											icon_color="#FFFFFF"
											handleExpand={(expanId) => handleExpand(expanId)}
											expanded={expanded}
											content={listInfos(informations)}
										/>
									</div>
								)}
							</div>

							<div className={`col-md-3`}>
								{!isPhone ? (
									<React.Fragment>
										<span className={classes['footer--title']}>{Identify.__('our app')}</span>
										<ul>
											<li>
												<div className={classes['download-icon']}>
													<div className="google-play">
														<a href={bianca_android_app} target="__blank">
															<img src="/images/google-play.png" alt="google-play" />
														</a>
													</div>
													<div className="app-store">
														<a href={bianca_ios_app} target="__blank">
															<img src="/images/app-store.png" alt="app-store" />
														</a>
													</div>
												</div>
											</li>
										</ul>
									</React.Fragment>
								) : (
									<div className={`footer-mobile download-app`}>
										<Expansion
											id={`expan-4`}
											title={Identify.__('Our app')}
											icon_color="#FFFFFF"
											handleExpand={(expanId) => handleExpand(expanId)}
											expanded={expanded}
											content={
												<React.Fragment>
													<ul>
														<li>
															<div className={classes['download-icon']}>
																<div className="google-play">
																	<a href={bianca_android_app} target="__blank">
																		<img src="/images/google-play.png" alt="google-play" />
																	</a>
																</div>
																<div className="app-store">
																	<a href={bianca_ios_app} target="__blank">
																		<img src="/images/app-store.png" alt="app-store" />
																	</a>
																</div>
															</div>
														</li>
													</ul>
												</React.Fragment>
											}
										/>
									</div>
								)}
							</div>
						</div>
					</div>
				</div>
				{/* <Copyright isPhone={isPhone} classes={classes} /> */}
				<div className={`mobile-social`}>
						{!isPhone ? (<></>):(
							<div className={`footer-mobile-social`}>
									<a href={footer_facebook} target="__blank">
										<div className={classes['facebook-icon social-icon']} ></div>
									</a>
									<a href={footer_twitter} target="__blank">
										<div className={classes['twitter-icon social-icon']} ></div>
									</a>
									<a href={footer_instagram} target="__blank">
										<div className={classes['instagram-icon social-icon']} ></div>
									</a>
							</div>
						)}
				</div>
			</div>
			<Chats history={history} isPhone={isPhone}/>
		</React.Fragment>
	);
};
export default Footer;
