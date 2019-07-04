import React from 'react';
import {configColor} from "src/simi/Config";
const $ = window.$;
class Abstract extends React.Component {
    constructor(props){
        super(props);
        this.parent = this.props.parent;
        this.key = this.props.id;
        this.selected = this.parent.selected;
        this.configColor = configColor;
        this.type_id = this.parent.getProductType()
    }
    
    setDefaultSelected = (val,multi=true)=>{
        let selectedKey = this.selected[this.key];
        if(!multi){
            if(selectedKey && selectedKey.length > 0){
                return parseInt(selectedKey[0],10);
            }
            return 0;
        }
        if(selectedKey){
            if(selectedKey.indexOf(val) > -1){
                return true;
            }
        }
        return false;
    };

    updateForBundle =(value,type)=>{
        if(this.parent.getProductType() === 'bundle'){
            let {data} = this.props;
            let item = data.selections[value];
            let input = $('.bundle-option-qty.'+type+' input');
            if(item){
                let qty = item.qty;
                input.val(qty);
                input.attr('data-value',value);
                let customQty = parseInt(item.customQty,10);
                if(customQty === 0){
                    input.attr('readonly','readonly');
                }else{
                    input.removeAttr('readonly')
                }
                $('#tier-prices-'+type+'-'+this.key+'').html(item.tierPriceHtml);
                return;
            }
            input.attr('data-value',0);
            $('.bundle-option-qty.'+type+' input').removeAttr('readonly');
            $('.bundle-option-qty.'+type+' input').val(0);
            $('#tier-prices-'+type+'').html('');
        }
    };

    updateSelected =(key,val)=>{
        this.parent.updateOptions(key,val);
    };

    deleteSelected =(key = this.key) => {
        this.parent.deleteOptions(key);
    };

    renderLableItem =(title,price,style={})=>{
        let symbol = price > 0 ? <span style={{margin:'0 10px'}}>+</span> : null;
        price = price > 0 ? this.props.parent.renderOptionPrice(price) : null;
        style = {...{
            display : 'flex',
            fontWeight: '400'
        },...style}
        let label  = <div style={style} className={`label-option-text`}>
                        <span style={{
                            fontSize: '16px',
                        }}>{title}</span>
                        {symbol}
                        {price}
                    </div>;
        return label;
    }
}
export default Abstract;