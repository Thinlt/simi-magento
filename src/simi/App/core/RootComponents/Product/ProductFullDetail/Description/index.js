import React from 'react'
import classes from './description.css'
import Identify from 'src/simi/Helper/Identify';
import RichText from 'src/simi/BaseComponents/RichText';

const Description = props => {
    const {product} = props
    return (
        <React.Fragment>
            <h2 className={classes.descriptionTitle}>
                <span>{Identify.__('Description')}</span>
            </h2>
            <RichText content={product.description.html} />
        </React.Fragment>
    )
}

export default Description