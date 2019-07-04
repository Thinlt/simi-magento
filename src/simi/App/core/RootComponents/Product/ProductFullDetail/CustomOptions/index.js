import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import OptionBase from '../OptionBase'
import Checkbox from '../OptionType/Checkbox';
import Radio from '../OptionType/Radio';
import Select from '../OptionType/Select';
import TextField from '../OptionType/Text';
import FileSelect from '../OptionType/File';
import { LazyComponent } from 'src/simi/BaseComponents/LazyComponent/'

const $ = window.$;
const DatePicker = (props)=>{
    return <LazyComponent component={()=>import('../OptionType/Date')} {...props}/>
}
const TimePicker = (props)=>{
    return <LazyComponent component={()=>import('../OptionType/Time')} {...props}/>
}
class CustomAbstract extends OptionBase {

    constructor(props){
        super(props);
        this.exclT = 0;
        this.inclT = 0;
    }

    renderOptions = () => {
        if(this.data instanceof Object && this.data.hasOwnProperty('custom_options')){
            let options = this.data.custom_options;
            if(!options) return <div></div>;
            let mainClass = this;
            let optionsHtml = options.map(function (item, index) {
                let labelRequired = mainClass.renderLabelRequired(parseInt(item.isRequired,10));
                if(parseInt(item.isRequired,10) === 1){
                    mainClass.required.push(item.id);
                }
                let priceLabel = "";
                let showType = 2;
                if (item.type === 'drop_down' || item.type === 'checkbox'
                    || item.type === 'multiple' || item.type === 'radio') {
                    showType = 1;
                }

                if (showType === 2) {
                    let itemPrice = item.values[0];
                    let prices = 0;
                    if (itemPrice.price) {
                        prices = itemPrice.price;
                    } else if (itemPrice.price_including_tax) {
                        prices = itemPrice.price_including_tax.price;
                    }
                    priceLabel = prices > 0 ?
                        <span className="option-price" style={{marginLeft : 10}}>+ {mainClass.renderOptionPrice(prices)}</span> : null;
                }
                return (
                    <div className="option-select" key={Identify.randomString(5)}>
                        <div className="option-title">
                            <span>{item.title}</span>
                            {labelRequired}
                            {priceLabel}
                        </div>
                        <div className="option-content">
                            <div className="option-list">
                                {mainClass.renderContentOption(item,item.type, showType)}
                            </div>

                        </div>
                    </div>
                );
            });
            return (
                <div className="custom-options">
                    <div id="customOption" style={{marginTop: '10px'}}>
                        {optionsHtml}
                    </div>
                </div>
            );
        }
    }

    renderContentOption = (ObjOptions, type, showType) => {
        let id = ObjOptions.id;
        
        if(type === 'multiple' || type === 'checkbox'){
            return this.renderMutilCheckbox(ObjOptions, id,showType)
        }
        if(type === 'radio'){
            return <Radio data={ObjOptions} id={id} parent={this} />
        }
        if(type === 'drop_down' || type === 'select' ){
            return <div style={{marginTop:-10}}>
                        <Select data={ObjOptions} id={id} parent={this}/>
                </div>
        }
        if(type === 'date'){
            return <div style={{marginTop:-10}}>
                        <DatePicker id={id} parent={this}/>
                    </div>
        }
        if(type === 'time'){
            return <div style={{marginTop:-10}}>
                    <TimePicker id={id} parent={this}/>
                </div>
        }
        if(type === 'date_time'){
            return (
                <div style={{marginTop:-10}}>
                    <DatePicker datetime={true} id={id} parent={this}/>
                    <TimePicker datetime={true} id={id} parent={this}/>
                </div>
            )
        }
        if(type === 'field'){
            return <TextField id={id} parent={this} max_characters={ObjOptions.max_characters}/>
        }
        if(type === 'area'){
            return <TextField id={id} parent={this} type={type}/>
        }
        
        if(type === 'file'){
            return <FileSelect data={ObjOptions} id={id} parent={this} type={type}/>
        }
    };

    renderMutilCheckbox =(ObjOptions, id = '0',showType)=>{
        let values = ObjOptions.values;
        let html = values.map(item => {
            let prices = 0;
            if (showType === 1) {
                if (item.price) {
                    prices = item.price;
                } else if (item.price_including_tax) {
                    prices = item.price_including_tax.price;
                }
            }
            let symbol = prices > 0 ? <span style={{margin:'0 10px'}}>+</span> : null;
            prices = prices > 0 ? <span className="child-price">{this.renderOptionPrice(prices)}</span> : null;
            let label  = <div style={{display : 'flex'}}>
                <span className="child-label">{item.title}</span>
                {symbol}
                {prices}
            </div>;
            return (
                <div key={Identify.randomString(5)} className="option-row">
                    <Checkbox id={id} label={label}  value={item.id} parent={this}/>
                </div>
            )
        });
        return html;
    };

    updatePrices = (selected = this.selected) => {
        let prices = this.parentObj.Price.state.prices;
        if (prices.show_ex_in_price === 1) {
            // prices.regural_price += this.inclT;
            prices.price_excluding_tax.price -= this.exclT;
            prices.price_including_tax.price -= this.inclT;
        } else {
            if (prices.regural_price) {
                prices.regural_price -= this.exclT;
            }
            prices.price -= this.exclT;
        }
        this.exclT = 0;
        this.inclT = 0;
        let customOptions = this.data.custom_options;
        let customSelected = selected;
        for (let c in customOptions) {
            let option = customOptions[c];
            for (let s in customSelected) {
                if (option.id === s) {
                    let selected = customSelected[s];
                    let values = option.values;
                    if (option.type === "date" || option.type === "time"
                        || option.type === "date_time" || option.type === "area"
                        || option.type === "field" || option.type === "file") {
                        let value = values[0];
                        if (value.price_excluding_tax) {
                            this.exclT += parseFloat(value.price_excluding_tax.price);
                            this.inclT += parseFloat(value.price_including_tax.price);
                        } else {
                            this.exclT += parseFloat(value.price);
                            this.inclT += parseFloat(value.price);
                        }
                    } else {
                        for (let v in values) {
                            let value = values[v];
                            if (Array.isArray(selected)) {
                                if (selected.indexOf(value.id) !== -1) {
                                    //add price
                                    if (value.price_excluding_tax) {
                                        this.exclT += parseFloat(value.price_excluding_tax.price);
                                        this.inclT += parseFloat(value.price_including_tax.price);
                                    } else {
                                        this.exclT += parseFloat(value.price);
                                        this.inclT += parseFloat(value.price);
                                    }
                                }
                            } else {
                                if (value.id === selected) {
                                    //add price
                                    if (value.price_excluding_tax) {
                                        this.exclT += parseFloat(value.price_excluding_tax.price);
                                        this.inclT += parseFloat(value.price_including_tax.price);
                                    } else {
                                        this.exclT += parseFloat(value.price);
                                        this.inclT += parseFloat(value.price);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if(prices.show_ex_in_price === 1){
            prices.price_excluding_tax.price += this.exclT;
            prices.price_including_tax.price += this.inclT;
        }else {
            prices.regural_price += this.exclT;
            prices.price += this.exclT;
        }
        this.parentObj.Price.updatePrices(prices);
    }

    setParamQty = ()=>{
        let qty = $('input.option-qty').val();
        this.params['qty'] = qty;
    };

    getParams = ()=>{
        if(!this.checkOptionRequired()){
            return false;
        }
        this.setParamOption('options');
        this.setParamQty();
        return this.params;
    }
    render(){
        return (
            <div>
                {this.renderOptions()}
            </div>
        )
    }
}
export default CustomAbstract;