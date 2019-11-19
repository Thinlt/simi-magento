import React from 'react';
import PropTypes from 'prop-types';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import Collapse from '@material-ui/core/Collapse';
import Identify from 'src/simi/Helper/Identify';
import {configColor} from 'src/simi/Config';

class NestedListItem extends React.Component {

    constructor(props){
        super(props);
        this.refListItem = React.createRef();
        this.state = { open: false };
    }

    handleClickOutside = (event) => {
        if (this.refListItem && this.refListItem.current && !this.refListItem.current.contains(event.target)) {
            this.closeMenu()
        }
    }

    componentDidMount(){
        document.addEventListener("mousedown", this.handleClickOutside);
    }

    handleClick = (event) => {
        this.setState(state => ({ open: !state.open }));
    };

    closeMenu = () => {
        this.setState(state => ({ open: false }));
    }

    shouldComponentUpdate(nextProps,nextState){
        return nextState.open !== this.state.open
    }

    render() {
        const className = this.props.className?this.props.className:'';
        const primaryText = Identify.isRtl()? <div style={{textAlign: 'right'}}>{this.props.primarytext}</div> : this.props.primarytext
        if (this.props.obj) {
            this.props.obj(this);
        }
        return (
            <div ref={this.refListItem}>
                <ListItem
                    button
                    className={className}
                    onClick={this.handleClick}
                    >
                    {this.props.listItemIcon}
                    <ListItemText primary={primaryText}
                                  primaryTypographyProps={{
                                      style:{color:this.props.color ? this.props.color:''}
                                  }}/>
                    {this.state.open ? <i className="icon-chevron-up icons"></i> : <i className="icon-chevron-down icons"></i>}
                </ListItem>
                <Collapse in={this.state.open} timeout="auto" unmountOnExit>
                    <List component="div" disablePadding>
                        {this.props.children}
                    </List>
                </Collapse>
            </div>
        );
    }
}

NestedListItem.propTypes = {
    primarytext: PropTypes.oneOfType([PropTypes.string,PropTypes.object]).isRequired,
    children: PropTypes.oneOfType([PropTypes.array,PropTypes.object]).isRequired,
    className: PropTypes.string,
    listItemIcon: PropTypes.object
};


export default NestedListItem;