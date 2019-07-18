import React from 'react'
import defaultClasses from './item.css';
import classify from 'src/classify';
import Price from 'src/simi/BaseComponents/Price';
import { Whitebtn } from 'src/simi/BaseComponents/Button';
import Identify from "src/simi/Helper/Identify";

const Item = props => {
    const { classes, item, history } = props;
    const location = {
        pathname: `/${item.url_key}.html`
    }

    const image = (
        <div 
            className={classes["harlows-product-image"]}
            style={{
                // borderColor: configColor.image_border_color,
                backgroundColor: 'white',
                // backgroundImage: `url("${item.images[0].url}")`
            }}
            onClick={() => history.push(location)}
            >
            <div style={{position:'absolute',top:0,bottom:0,width: '100%', padding: 1}}>
                <img src={item.small_image.url} alt={item.name}/>
            </div>
        </div>
    )

    const action = (
        <div className={classes["product-item-action"]}>
            <Whitebtn 
                className={`${classes["view-link"]} ${classes["full-btn"]}`} 
                text={Identify.__('View')}
                onClick={() => history.push(location)}
            />
        </div>
    )

    return (
        <div className={`${classes['product-item']} ${classes['harlows-product-grid-item']} ${classes["two-btn"]}`}>
            {/* {this.props.lazyImage?
                (<LazyLoad placeholder={<div className="harlows-product-image"/>}>
                    {image}
                </LazyLoad>):
                image
            } */}
            {image}
            <div className={classes["harlows-product-des"]}>
                <div className={`${classes["product-name"]} ${classes['small']}`} onClick={()=> history.push(this.location)}>
                    {item.name}
                </div>
                <div className={classes["prices-layout"]} id={`price-${item.entity_id}`} onClick={()=>history.push(this.location)}>
                    <Price prices={item.price} type={item.type_id} classes={classes}/>
                </div>
            </div>
            {action}
        </div>
    );
}

export default classify(defaultClasses)(Item)