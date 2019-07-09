import React, { Component } from 'react';

class Quantity extends Component {
    render() {
        const { classes, initialValue, onValueChange } = this.props;
        return (
            <div className={classes['product-quantity']}>
                <input defaultValue={initialValue} onChange={onValueChange}/>
            </div>
        );
    }
}

export default Quantity;
