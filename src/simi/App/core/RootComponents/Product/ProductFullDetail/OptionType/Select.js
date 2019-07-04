import React from 'react';
import Abstract from "./Abstract";
import Identify from "src/simi/Helper/Identify"
import SelectField from '@material-ui/core/Select';
import MenuItem from '@material-ui/core/MenuItem';
import { withStyles } from '@material-ui/core/styles';
import FormControl from '@material-ui/core/FormControl';
import {configColor} from "src/simi/Config";

const styles = {
    formControl : {
        color : configColor.button_background
    }
}
class Select extends Abstract {

    constructor(props){
        super(props);
        let value = this.setDefaultSelected(0,false);
        this.state = {
            value
        };
    }

    componentDidMount(){
        if(this.state.value !== 0){
            this.updateForBundle(this.state.value,'select');
        }
    }

    handleChange = (event, index) => {
        this.setState({ [event.target.name]: event.target.value });
        let value = event.target.value.toString();
        let key = this.key;
        if(value !== 0){
            this.updateSelected(key,value);
        }else{
            this.deleteSelected();
        }
        this.updateForBundle(value,'select');
    };



    renderWithBundle = (data) => {
        let options = data.selections;
        let items = [];
        for (let i in options) {
            let item = options[i];
            let price = 0;
            if (item.price) {
                price = item.price;
            }
            if (item.priceInclTax) {
                price = item.priceInclTax;
            }
            if (Identify.magentoPlatform() === 2) {
                price = parseFloat(item.prices.finalPrice.amount);
            }
            let element = (
                <MenuItem key={Identify.randomString(5)} name={this.props.key_field} value={parseInt(i,10)}>
                    <div className="option-row" style={{alignItems : 'center',fontFamily: 'Montserrat , sans-serif'}}>
                        {this.renderLableItem(item.name,price,{alignItems : 'center'})}
                    </div>
                </MenuItem>
            );
            items.push(element);
        }
        return items;
    };

    renderWithCustom = (data) => {
        let values = data.values;
        if(values instanceof Array && values.length > 0){
            let items = values.map(item => {
                let prices = 0;
                if (item.price) {
                    prices = item.price;
                } else if (item.price_including_tax) {
                    prices = item.price_including_tax.price;
                }

                return (
                    <MenuItem key={Identify.randomString(5)} value={parseInt(item.id,10)}>
                        <div className="option-row" style={{alignItems : 'center'}}>
                            {this.renderLableItem(item.title,prices)}
                        </div>
                    </MenuItem>
                );

            });
            return items;
        }
        return <div></div>
    };

    render = () => {
        let {data} = this.props;
        let type_id = this.props.parent.getProductType();
        let items = null;
        if(type_id === 'bundle'){
            items = this.renderWithBundle(data);
        }else {
            items = this.renderWithCustom(data)
        }
        return (
            <div className="option-value-item-select">
                <FormControl  style={{color : '#333',marginTop:20}} fullWidth={true}>
                    <SelectField
                        value={this.state.value}
                        onChange={this.handleChange}
                        inputProps={{
                            name: 'value',
                            id: 'selection',
                        }}
                    >
                        <MenuItem key={Identify.randomString(5)} value={0}>
                            <div className="option-row" style={{alignItems : 'center',fontSize:16,fontWeight:100}}>
                                <em>{Identify.__('Choose a selection')}</em>
                            </div>
                        </MenuItem>
                        {items}
                    </SelectField>
                </FormControl>

            </div>

        );
    }
}

export default withStyles(styles)(Select);