import React from 'react';
import Identify from '/src/simi/Helper/Identify'
import defaultClasses from './filter.css';
import {Loading} from "src/simi/BaseComponents/Loading";
import Checkbox from 'src/simi/BaseComponents/Checkbox'
import Dropdownplus from 'src/simi/BaseComponents/Dropdownplus'
import {Whitebtn} from 'src/simi/BaseComponents/Button'
import { mergeClasses } from 'src/classify'
import { withRouter } from 'src/drivers';

const $ = window.$;

class Filter extends React.Component {

    constructor(props) {
        super(props);
        this.state = {...this.state, ...{loaded: true}}
        this.rowFilterAttributes = []
        this.rowsActived = []
        this.filtersToApply = {}
        this.classes = mergeClasses(this.props.classes, defaultClasses)
    }

    renderActivedFilter() {
        const {props, classes} = this

        if (props.data)
            this.items = props.data;

        if (this.items.layer_state) {
            if (this.activedItems !== this.items.layer_state) {
                this.activedItems = this.items.layer_state;
                this.rowsActived = this.activedItems.map((activedItem, index) => {
                    let styles = {}
                    if (index === 0)
                        styles.marginTop = 0 

                    return (
                        <div key={Identify.randomString(5)} className={classes["active-filter-item"]}>
                            <div className={classes["filter-name"]} style={styles}>
                                <span className={`${classes['filter-name-text']} ${classes['root-menu']}`}>{Identify.__(activedItem.title)}</span>
                            </div>
                            {
                                <Checkbox
                                    classes={this.classes}
                                    key={Identify.randomString(5)}
                                    className={classes["filter-item"]}
                                    onClick={() => this.deleteFilter(activedItem.attribute)}
                                    label={activedItem.label}
                                    selected={true}
                                /> 
                            }
                        </div>
                    );
                });
            }
            return (
                <div>{this.rowsActived}</div>
            );
        }
    }
    
    renderFilterItems() {
        const {props, classes} = this

        if (props.data)
            this.items = props.data;
        if (this.items && this.items.length !== 0) {
            if (this.filterAttributes !== this.items) {
                this.filterAttributes = this.items
                this.rowFilterAttributes = []
                this.filterAttributes.map((item, index) => {
                    let styles = {}
                    if (index === 0 && !this.items.layer_state)
                        styles.marginTop = 0 
                    const name = <span className={`${classes['filter-name-text']} ${classes['root-menu']}`}>{Identify.__(item.name)}</span>
                    let filterOptions = this.renderFilterItemsOptions(item)
                    if (filterOptions.length > 0) {
                        this.rowFilterAttributes.push(
                            this.state.isPhone?
                            <Dropdownplus 
                                key={Identify.randomString(5)}
                                classes={this.classes}
                                title={Identify.__(item.name)}
                                expanded={this.filtersToApply[item.request_var]?true:false}
                            >
                                <div 
                                    id={`filter-option-items-${item.request_var}`} 
                                    className={classes["filter-option-items"]}>
                                    {this.renderFilterItemsOptions(item)}
                                </div>
                            </Dropdownplus>
                            :
                            <div key={Identify.randomString(5)}>
                                <div className={classes["filter-name"]} style={styles}>{name}</div>
                                <div className={classes["filter-option-items"]}>{this.renderFilterItemsOptions(item)}</div>
                            </div>
                        )
                    }
                    return null
                }, this);
            }
            return (
                <div>{this.rowFilterAttributes}</div>
            );
        }
    }

    renderFilterItemsOptions(item)
    {
        const {props, classes} = this
        let options= [];
        if(item){
            if(item.filter_items !== null){
                options = item.filter_items.map(function (optionItem) {
                    const name = <span className={classes["filter-item-text"]}>
                        {$("<div/>").html(Identify.__(optionItem.label)).text()}
                        </span>;
                    return (
                        <Checkbox
                            key={Identify.randomString(5)}
                            id={`filter-item-${item.request_var}-${optionItem.value_string}`}
                            className={classes["filter-item"]}
                            classes={classes}
                            onClick={(e)=>{
                                this.clickedFilter(item.request_var, optionItem.value_string);
                            }}
                            label={name}
                            selected={this.filtersToApply[item.request_var] === optionItem.value_string}
                        />
                    );
                }, this, item);
            }
        }
        return options
    };
    
    renderClearButton() {
        const { classes} = this
        return this.state.isPhone?'':
        (this.items.layer_state)
        ? (<div className={classes["action-clear"]}>
                <div 
                    role="presentation"
                    onClick={() => this.clearFilter()}
                    className={classes["clear-filter"]}>{Identify.__('Clear all')}</div>
            </div>
        ) : <div className={classes["clear-filter"]}></div>
    }

    renderApplyButton() {
        return (
            <div style={{padding: '10px 15px 0 15px'}}>
                <Whitebtn 
                    onClick={()=>this.applyFilter()}
                    text={Identify.__('Apply')}
                />
            </div>
        )
    }

    clickedFilter(attribute, value) {
        const {history, location} = this.props
        const { search } = location;
        if (attribute) {
            const filterParams = []
            filterParams.push({code:attribute, value: value})
            const queryParams = new URLSearchParams(search);
            queryParams.set('filter', JSON.stringify(filterParams));
            history.push({ search: queryParams.toString() });
        }
    }

    componentDidMount(){
        let obj = this;
        $('.top-filter-button').click(function () {
            if(!obj.state.loaded){
                setTimeout(()=>{
                    obj.setState({loaded:true})
                },1000)
            }
        })
    }
    
    render() {
        if(!this.state.loaded){
            return <Loading/>
        }
        const {props, classes} = this
        this.items = props.data?this.props.data:null;
        let activeFilter = this.items.layer_state?
            (
                <div className={classes["active-filter"]}>
                    {this.renderActivedFilter()}
                </div>
            ):
            ''
        const filterProducts = 
                <div className={`${classes['filter-products']}`}>
                    {this.renderClearButton()}
                    {activeFilter}
                    {this.renderFilterItems()} 
                    {this.state.isPhone && this.renderApplyButton()}
                </div>
        if (this.rowsActived.length === 0 && this.rowFilterAttributes.length === 0)
                return ''
                
        return this.state.isPhone?
        <Dropdownplus
            className={classes["siminia-phone-filter"]}
            title={Identify.__('Filter')}
        >
            {filterProducts}
        </Dropdownplus>
        :filterProducts;
    }
}

export default (withRouter)(Filter);