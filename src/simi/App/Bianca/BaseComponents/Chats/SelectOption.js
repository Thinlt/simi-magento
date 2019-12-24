import React from 'react';
import Identify from "src/simi/Helper/Identify";
require('./SelectOption.scss');

class Select extends React.Component {
    state = {
        isOpen: false,
        selected: {value: '', label: ''},
        ...this.props
    }
    
    constructor(props){
        super(props);
        this.selectId = Identify.randomString(2);
        this.isClickToggle = false;
    }

    onClickItem = (item) => {
        if (this.props.onChange) {
            this.props.onChange(item.value);
        }
        this.setState({selected: item});
    }

    onClickTriggerRef = (e) => {
        e.preventDefault();
        e.stopPropagation();
        const {triggerRef} = this.props;
        if (triggerRef){
            this.setState((state) => ({isOpen: !state.isOpen}));
        }
    }

    onToggle = (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.isClickToggle = true;
        this.setState((state) => {
            return {isOpen: !state.isOpen};
        });
    }

    onClickOutside = (e) => {
        if (!this.isClickToggle) {
            this.setState({isOpen: false});
        }
        this.isClickToggle = false;
    }

    componentDidMount(){
        const {triggerRef} = this.props;
        document.addEventListener("click", this.onClickOutside);
        if (triggerRef && triggerRef.current) {
            triggerRef.current.addEventListener('click', this.onClickTriggerRef)
        }
    }

    componentWillUnmount(){
        const {triggerRef} = this.props;
        document.removeEventListener("click", this.onClickOutside);
        if (triggerRef && triggerRef.current) {
            triggerRef.current.removeEventListener('click', this.onClickTriggerRef)
        }
    }

    options = () => {
        const {selected} = this.state;
        let _items = []
        if (this.props.items){
            Object.values(this.props.items).forEach((item, index) => {
                _items.push(<span className={`input-option ${selected.value === item.value ? 'selected':''}`} 
                    onClick={(e) => this.onClickItem(item)} key={index}>{item.label}</span>
                );
            });
        }
        return _items;
    };

    render(){
        const {selected, isOpen} = this.state;
        const {showSelected, className, hiddenInput} = this.props;
        const placeholder = this.props.placeholder || Identify.__('Please select');
        return (
            <div className={className} onClick={(e) => this.onToggle(e)}>
            {
                showSelected && <div className="input-display">{selected ? selected.label : placeholder}</div>
            }
            {
                isOpen && <div className={"simi-simple-input-select"}>{this.options()}</div>
            }
            {
                hiddenInput && typeof hiddenInput === 'object' ?
                    <input type="hidden" {...hiddenInput} defaultValue={selected && selected.value || ''} /> :
                hiddenInput === true ?
                    <input type="hidden" style={{display: "none"}} name="input-select" defaultValue={selected && selected.value || ''} /> : null
            }
            </div>
        );
    }

}
export default Select