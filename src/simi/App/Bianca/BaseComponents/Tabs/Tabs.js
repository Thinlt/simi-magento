import React, { Component } from 'react';
import PropTypes from 'prop-types';
import Tab from './Tab';
require('./style.scss');

class Tabs extends Component {
    static propTypes = {
        children: PropTypes.instanceOf(Array).isRequired,
    }

    constructor(props) {
        super(props);
        let activeItem = this.props.children[0];
        if (this.props.activeItem !== undefined && this.props.activeItem !== null) {
            activeItem = this.props.children[this.props.activeItem];
        }
        if (this.props.children) {
            const activeItemFind = this.props.children.find((item) => {
                return item.props.isActive === 1;
            });
            if (activeItemFind) activeItem = activeItemFind;
        }
        this.state = {
            activeTab: activeItem.props.label,
        };
        if (this.props.objRef) {
            this.props.objRef(this);
        }
    }

    componentDidUpdate(){
        if (this.state.scrollTo) {
            this.state.scrollTo();
        }
    }

    onClickTabItem = (tab) => {
        this.setState({ activeTab: tab});
    }

    openTab = (tabId) => {
        if(this.props.children[tabId]){
            let newState = {}
            if (this.props.scrollTo) {
                newState.scrollTo = this.props.scrollTo;
            }
            this.setState({ activeTab: this.props.children[tabId].props.label, ...newState});
        }
    }

    render() {
        const {
            onClickTabItem,
            props: {
                children,
            },
            state: {
                activeTab,
            }
        } = this;

        return (
            <div className="tabs">
                <div className="tab-menu">
                    <ol className="tab-list">
                        {children.map((child) => {
                            const { label } = child.props;
                            return (
                                <Tab
                                    activeTab={activeTab}
                                    key={label}
                                    label={label}
                                    onClick={onClickTabItem}
                                />
                            );
                        })}
                    </ol>
                </div>
                <div className="tab-content">
                    {children.map((child) => {
                        if (child.props.label !== activeTab) return undefined;
                        return child.props.children;
                    })}
                </div>
            </div>
        );
    }
}

export default Tabs;