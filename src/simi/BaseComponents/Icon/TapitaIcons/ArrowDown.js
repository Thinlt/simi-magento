/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 6/22/18
 * Time: 9:44 AM
 */
import React from 'react'
import Abstract from './Abstract'
class Icon extends Abstract {
    render(){
        return this.renderSvg('0 0 24 24',<path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"></path>)
    }
}
export default Icon