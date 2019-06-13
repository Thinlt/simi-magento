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
        return this.renderSvg('0 0 24 24',<path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"></path>)
    }
}
export default Icon