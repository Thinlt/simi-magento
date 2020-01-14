import React, { Component } from 'react';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import PropTypes from 'prop-types';
import { toggleCart } from 'src/simi/Redux/actions/simiactions';
import CartCounter from './cartCounter';
import Basket from "src/simi/App/Bianca/BaseComponents/Icon/Basket";
import classify from 'src/classify';
import defaultClasses from './cartTrigger.css';
import { Link, resourceUrl } from 'src/drivers';
// import Identify from 'src/simi/Helper/Identify'


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
                <div className='item-icon' style={{display: 'flex', justifyContent: 'center'}}>  
                    <Basket style={{display: 'block', margin: 0}}/>
                </div>
            </React.Fragment>
        )
    }

    render() {            
        const {
            classes,
            toggleCart,
            cart: { details },
            isPhone
        } = this.props;
        
        const { cartIcon } = this;
        const itemsQty = details.items_qty;
        return (
            <div>
                {isPhone
                ?
                    <Link 
                        to={resourceUrl('/cart.html')}
                        className='cart-trigger-root'
                        aria-label="Open cart page"
                    >
                        {cartIcon}
                        <CartCounter counter={itemsQty ? itemsQty : 0} />
                    </Link>
                :
                    <button
                        className='cart-trigger-root'
                        aria-label="Toggle mini cart"
                        onClick={toggleCart}
                    >
                        {cartIcon}
                        <CartCounter counter={itemsQty ? itemsQty : 0} />
                    </button>
                }
            </div>
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
