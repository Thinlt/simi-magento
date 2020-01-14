import React from 'react';
import Identify from "src/simi/Helper/Identify";
require('./Select.scss');

class Select extends React.Component {
    state = {
        isOpen: false,
        selected: this.props.selected || null,
        ...this.props
    }
    
    constructor(props){
        super(props);
        this.selectId = Identify.randomString(2);
        this.isClickToggle = false;
    }

    static getDerivedStateFromProps(props, state){
        if (props.selected) state.selected = props.selected;
        return state;
    }

    onClickItem = (item) => {
        if (this.props.onChange) {
            this.props.onChange(item.value);
        }
        if (this.props.onChangeItem) {
            this.props.onChangeItem(item);
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
                _items.push(<span className={`input-option ${selected && selected.value === item.value ? 'selected':''}`} 
                    onClick={(e) => this.onClickItem(item)} key={index}>{item.label}</span>
                );
            });
        } else if(this.props.children) {
            Object.values(this.props.children).forEach((item, index) => {
                _items.push(<div className={`input-option ${selected && selected.value === item.props.value ? 'selected':''}`} 
                    onClick={(e) => this.onClickItem({label: item.props.children, value: item.props.value})} key={`${index}-${item.props.value}`}>{item.props.children}</div>
                );
            });
        }
        return _items;
    };

    render(){
        const {isOpen} = this.state;
        const selected = this.props.forceSelected || this.state.selected;
        const {showSelected, className, hiddenInput} = this.props;
        const placeholder = <div className="placeholder">{this.props.placeholder || Identify.__('Please select')}</div>;
        return (
            <div className={`simi-input-select ${className}`} onClick={(e) => this.onToggle(e)}>
                {this.props.icon && 
                    <div className="icon">{this.props.icon}</div>
                }
                {showSelected && 
                    <div className="input-display">{selected && selected.label ? selected.label : placeholder}</div>
                }
                {isOpen && 
                    <div className={"simi-simple-input-select"}>{this.options()}</div>
                }
                {hiddenInput && typeof hiddenInput === 'object' ?
                    <input type="hidden" {...hiddenInput} defaultValue={selected && selected.value || ''} /> :
                    hiddenInput === true ?
                    <input type="hidden" style={{display: "none"}} name="input-select" defaultValue={selected && selected.value || ''} /> : null
                }
            </div>
        );
    }

}
export default Select