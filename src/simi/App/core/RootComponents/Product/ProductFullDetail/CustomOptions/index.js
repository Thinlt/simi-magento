import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import OptionBase from '../OptionBase'
import Checkbox from '../OptionType/Checkbox';
import Radio from '../OptionType/Radio';
import Select from '../OptionType/Select';
import TextField from '../OptionType/Text';
import FileSelect from '../OptionType/File';
import { LazyComponent } from 'src/simi/BaseComponents/LazyComponent/'
import OptionLabel from '../OptionLabel'
import defaultClasses from './customoptions.css'
import classify from 'src/classify';

const DatePicker = (props)=>{
    return <LazyComponent component={()=>import('../OptionType/Date')} {...props}/>
}
const TimePicker = (props)=>{
    return <LazyComponent component={()=>import('../OptionType/Time')} {...props}/>
}
class CustomOptions extends OptionBase {

    constructor(props){
        super(props);
        this.exclT = 0;
        this.inclT = 0;
    }

    renderOptions = () => {
        const {classes} = this.props
        if(this.data instanceof Object && this.data.hasOwnProperty('custom_options')){
            const options = this.data.custom_options;
            if(!options) return <div></div>;
            const mainClass = this;
            const optionsHtml = options.map(function (item) {
                const labelRequired = mainClass.renderLabelRequired(parseInt(item.isRequired,10));
                if(parseInt(item.isRequired,10) === 1){
                    mainClass.required.push(item.id);
                }
                
                let priceLabel = "";
                if (item.type === 'drop_down' || item.type === 'checkbox'
                    || item.type === 'multiple' || item.type === 'radio') {
                } else {
                    priceLabel = <OptionLabel item={item.values[0]} classes={classes} />
                }

                return (
                    <div className={classes["option-select"]} key={Identify.randomString(5)}>
                        <div className={classes["option-title"]}>
                            <span>{item.title}</span>
                            {labelRequired}
                            {priceLabel}
                        </div>
                        <div className={classes["option-content"]}>
                            <div className={classes["option-list"]}>
                                {mainClass.renderContentOption(item,item.type)}
                            </div>
                        </div>
                    </div>
                );
            });
            return (
                <div className={classes["custom-options"]}>
                    <div id="customOption" style={{marginTop: '10px'}}>
                        {optionsHtml}
                    </div>
                </div>
            );
        }
    }

    renderContentOption = (ObjOptions, type) => {
        const id = ObjOptions.id;
        const {classes} = this.props
        
        if(type === 'multiple' || type === 'checkbox'){
            return this.renderMutilCheckbox(ObjOptions, id)
        }
        if(type === 'radio'){
            return <Radio data={ObjOptions} id={id} parent={this} classes={classes}/>
        }
        if(type === 'drop_down' || type === 'select' ){
            return <div style={{marginTop:-10}}>
                        <Select data={ObjOptions} id={id} parent={this} classes={classes}/>
                </div>
        }
        if(type === 'date'){
            return <div style={{marginTop:-10}}>
                        <DatePicker id={id} parent={this} classes={classes}/>
                    </div>
        }
        if(type === 'time'){
            return <div style={{marginTop:-10}}>
                    <TimePicker id={id} parent={this} classes={classes}/>
                </div>
        }
        if(type === 'date_time'){
            return (
                <div style={{marginTop:-10}}>
                    <DatePicker datetime={true} id={id} parent={this} classes={classes}/>
                    <TimePicker datetime={true} id={id} parent={this} classes={classes}/>
                </div>
            )
        }
        if(type === 'field'){
            return <TextField id={id} parent={this} max_characters={ObjOptions.max_characters} classes={classes}/>
        }
        if(type === 'area'){
            return <TextField id={id} parent={this} type={type} classes={classes}/>
        }
        
        if(type === 'file'){
            return <FileSelect data={ObjOptions} id={id} parent={this} type={type} classes={classes}/>
        }
    };

    renderMutilCheckbox =(ObjOptions, id = '0')=>{
        const {classes} = this.props
        const values = ObjOptions.values;
        const html = values.map(item => {
            return (
                <div key={Identify.randomString(5)} className={classes["option-row"]}>
                    <Checkbox id={id} item={item} value={item.id} parent={this} classes={classes}/>
                </div>
            )
        });
        return html;
    };

    updatePrices = (selected = this.selected) => {
        const prices = this.parentObj.Price.state.prices;
        prices.minimalPrice.excl_tax_amount.value -= this.exclT;
        prices.minimalPrice.amount.value -= this.inclT;
        this.exclT = 0;
        this.inclT = 0;
        const customOptions = this.data.custom_options;
        const customSelected = selected;
        for (const c in customOptions) {
            const option = customOptions[c];
            for (const s in customSelected) {
                if (option.id === s) {
                    const selected = customSelected[s];
                    const values = option.values;
                    if (option.type === "date" || option.type === "time"
                        || option.type === "date_time" || option.type === "area"
                        || option.type === "field" || option.type === "file") {
                            const value = values[0];
                        if (value.price_excluding_tax) {
                            this.exclT += parseFloat(value.price_excluding_tax.price);
                            this.inclT += parseFloat(value.price_including_tax.price);
                        } else {
                            this.exclT += parseFloat(value.price);
                            this.inclT += parseFloat(value.price);
                        }
                    } else {
                        for (const v in values) {
                            const value = values[v];
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
        prices.minimalPrice.excl_tax_amount.value += this.exclT;
        prices.minimalPrice.amount.value += this.inclT;

        this.parentObj.Price.updatePrices(prices);
    }

    setParamQty = ()=>{
        const qty = $('input.option-qty').val();
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
export default classify(defaultClasses)(CustomOptions);
