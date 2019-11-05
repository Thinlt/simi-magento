import React, { Component } from 'react';
import { arrayOf, object, shape, string } from 'prop-types';
import { List } from '@magento/peregrine';

import Tile from './tile';
require ('./tileList.scss');

class TileList extends Component {
    constructor(props){
        super(props)
    }
    static propTypes = {
        items: arrayOf(object)
    };
    
    render() {
        const {items} = this.props
        return <List renderItem={Tile} {...this.props} />;
    }
}

export default TileList;
