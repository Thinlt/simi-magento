import React from 'react';
import Abstract from "./Abstract";
import Radio from '@material-ui/core/Radio';
import RadioGroup from '@material-ui/core/RadioGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Identify from "src/simi/Helper/Identify";

class RadioField extends Abstract {
    constructor(props) {
        super(props);
        const defaultValue = this.setDefaultSelected(0,false).toString();
        this.state = {
            value : defaultValue
        };
        this.showTier = false;
        if(this.type_id === 'bundle'){
            const defaultItem = defaultValue !== 0 ? this.props.data.selections[defaultValue] : {};
            this.showTier = defaultItem.tierPrice && defaultItem.tierPrice.length > 0;
        }

    }

    updateCheck = (e,val)=> {
        this.setState({ value: val });
        this.updateSelected(this.key,val);
        this.updateForBundle(val,'radio');
        // $('.radio-option-'+this.key+'').find('svg').css({
        //     fill : '#000000de',
        //     color : '#000000de'
        // });
        // let id = `.radio-option-${val}`;
        // $(id).find('svg').css('fill',this.configColor.button_background)
        // $(id).find('svg').css('color',this.configColor.button_text_color)
    };

    renderWithBundle = (data)=>{
        const options = data.selections;
        const items = [];
        const {classes} = this.props
        for (const i in options) {
            const item = options[i];
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

            const label  = this.renderLableItem(item.name,price);

            const element = (
                <FormControlLabel
                    className={`radio-option-${this.key} radio-option-${i}`}
                    key={i}
                    value={i}
                    label={label}
                    control={<Radio classes={{
                        root: classes.root,
                        checked: classes.checked,
                    }}/>}
                />
            );
            items.push(element);
        }
        return items;
    };

    renderWithCustom = (data)=>{
        const values = data.values;
        const {classes} = this.props
        const items = values.map(item => {
            let prices = 0;
            if (item.price) {
                prices = item.price;
            } else if (item.price_including_tax) {
                prices = item.price_including_tax.price;
            }
            return (
                <FormControlLabel
                    className={`radio-option-${this.key} radio-option-${item.id}`}
                    key={item.id}
                    value={item.id}
                    label={this.renderLableItem(item.title,prices)}
                    control={<Radio classes={{
                        root: classes.root,
                        checked: classes.checked,
                    }}/>}
                />
            )
        })
        return items;
    };

    componentDidMount(){
        if(this.showTier){
            this.updateForBundle(this.state.value,'radio');
        }
    }

    render = () => {
        const {data, classes} = this.props;
        let items = null;
        if(this.type_id === 'bundle'){
            items = this.renderWithBundle(data);
        }
        else{
            items = this.renderWithCustom(data);
        }
        return (
            <div>
                <RadioGroup value={this.state.value} onChange={this.updateCheck}  name="radioOptions">
                    {items}
                </RadioGroup>
                <div className={classes["option-tier-prices"]} id={`tier-prices-radio-${this.key}`}></div>
            </div>

        );
    }
}
RadioField.defaultProps = {
    type : 1
};
export default RadioField;