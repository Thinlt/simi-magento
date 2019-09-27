import React, { Component } from 'react';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import PropTypes from 'prop-types';
import { toggleCart } from 'src/actions/cart';
import CartCounter from './cartCounter';
import Basket from "src/simi/App/Bianca/BaseComponents/Icon/Basket";
import classify from 'src/classify';
import defaultClasses from './cartTrigger.css'
// import Identify from 'src/simi/Helper/Identify'
import { Link } from 'src/drivers'
import { resourceUrl } from 'src/simi/Helper/Url'


export class Trigger extends Component {
    static propTypes = {
        children: PropTypes.node,
        classes: PropTypes.shape({
            root: PropTypes.string
        }),
        toggleCart: PropTypes.func.isRequired,
        itemsQty: PropTypes.number
    };

    get cartIcon() {
        const {
            classes,
            cart: { details }
        } = this.props;
        const itemsQty = details.items_qty;
        const iconColor = 'rgb(var(--venia-text))';
        const svgAttributes = {
            stroke: iconColor
        };

        if (itemsQty > 0) {
            svgAttributes.fill = iconColor;
        }
        return (
            <React.Fragment>
                <div className={classes['item-icon']} style={{display: 'flex', justifyContent: 'center'}}>  
                    <Basket style={{display: 'block', margin: 0}}/>
                </div>
            </React.Fragment>
        )
    }

    render() {            
        const {
            classes,
            //toggleCart,
            cart: { details }
        } = this.props;
        const { cartIcon } = this;
        const itemsQty = details.items_qty;
        return (
            <Link 
                to={resourceUrl('/cart.html')}
                className={classes.root}
                aria-label="Open cart page"
            >
                {cartIcon}
                <CartCounter counter={itemsQty ? itemsQty : 0} />
            </Link>
        )
    }
}

const mapStateToProps = ({ cart }) => ({ cart });

const mapDispatchToProps = {
    toggleCart,
};

export default compose(
    classify(defaultClasses),
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(Trigger);