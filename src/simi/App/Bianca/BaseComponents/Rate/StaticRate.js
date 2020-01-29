import React from  'react';
import FiveStars from 'src/simi/App/Bianca/BaseComponents/Icon/FiveStars'
import {configColor} from 'src/simi/Config';;
import PropTypes from 'prop-types';

const StaticRate = props => {
    const {classes, size, width, rate, isRtl} = props
    const height = size
    // const width = 5 * height
    const rateWidth = width * rate / 5;
    const starStyle = isRtl ? 
        {height: `${size}px`, width: `${rateWidth}px`, overflow: 'hidden', position: 'absolute', right: 0, top: 0} : 
        {height: `${size}px`, width: `${rateWidth}px`, overflow: 'hidden', position: 'absolute', left: 0, top: 0}
    
    return (
        <div className={classes["static-rate"]} style={{width: `${width}px`, height: height, position:'relative', marginTop: '1px'}}>
            <FiveStars style={{width: `${width}px`, height: `${height}px`, fill:'#e0e0e0'}}/>
            <div className={classes["static-rate-active"]} style={starStyle}>
                <FiveStars style={{width: `${width}px`, height: `${height}px`, fill:configColor.button_background}}/>
            </div>
        </div>
    )
}
StaticRate.defaultProps = {
    rate : 0,
    size : 15,
    width : 80,
    classes: {},
};
StaticRate.propTypes = {
    rate : PropTypes.number,
    size : PropTypes.number,
    classes : PropTypes.object,
};
export default StaticRate;