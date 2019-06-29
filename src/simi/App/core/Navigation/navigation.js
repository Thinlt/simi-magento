import React, { PureComponent } from 'react';
import { bool, func, object, shape, string } from 'prop-types';
import classify from 'src/classify';
import CategoryTree from './categoryTree'
import defaultClasses from './navigation.css'
import Identify from 'src/simi/Helper/Identify'
import Dashboardmenu from './Dashboardmenu'
import { withRouter } from 'react-router-dom';
import { compose } from 'redux';

class Navigation extends PureComponent {
    static propTypes = {
        classes: shape({
            authBar: string,
            body: string,
            form_closed: string,
            form_open: string,
            root: string,
            root_open: string,
            signIn_closed: string,
            signIn_open: string
        }),
        closeDrawer: func.isRequired,
        completePasswordReset: func.isRequired,
        createAccount: func.isRequired,
        email: string,
        firstname: string,
        forgotPassword: shape({
            email: string,
            isInProgress: bool
        }),
        getAllCategories: func.isRequired,
        getUserDetails: func.isRequired,
        isOpen: bool,
        isSignedIn: bool,
        lastname: string,
        resetPassword: func.isRequired,
        signInError: object
    };

    static getDerivedStateFromProps(props, state) {
        if (!state.rootNodeId && props.rootCategoryId) {
            return {
                ...state,
                rootNodeId: props.rootCategoryId
            };
        }

        return state;
    }

    componentDidMount() {
        this.props.getUserDetails();
        this.props.getAllCategories();
        this.setIsPhone();
    }

    state = {
        isCreateAccountOpen: false,
        isSignInOpen: false,
        isForgotPasswordOpen: false,
        rootNodeId: null,
        currentPath: null,
        isPhone: window.innerWidth < 1024,
    };

    setIsPhone(){
        const obj = this;
        $(window).resize(function () {
            const width = window.innerWidth;
            const isPhone = width < 1024;
            if(obj.state.isPhone !== isPhone){
                obj.setState({isPhone})
            }
        })
    }

    get categoryTree() {
        const { props, setCurrentPath, state } = this;
        const { rootNodeId } = state;
        const { closeDrawer } = props;

        return rootNodeId ? (
            <CategoryTree
                rootNodeId={props.rootCategoryId}
                currentId={rootNodeId}
                updateRootNodeId={setCurrentPath}
                onNavigate={closeDrawer}
            />
        ) : null;
    }

    setCurrentPath = currentPath => {
        const path = currentPath.split('/').reverse();
        const rootNodeId = parseInt(path[0]);

        this.setState(() => ({
            rootNodeId: rootNodeId,
            currentPath: path
        }));
    };

    setRootNodeIdToParent = () => {
        const path = this.state.currentPath;
        const parentId =
            path.length > 1 ? parseInt(path[1]) : this.props.rootCategoryId;
        path.shift();

        this.setState(() => ({
            rootNodeId: parentId,
            currentPath: path
        }));
    };


    renderDashboardMenu(className, jsonSimiCart) {
        const {
            classes,
            closeDrawer,
            isOpen,
            rootCategoryId
        } = this.props;

        let leftMenuItems = null
        let bottomMenuItems = null
        let config = null
        if (jsonSimiCart && jsonSimiCart['app-configs'] && jsonSimiCart['app-configs'][0] && jsonSimiCart['app-configs'][0].app_settings) {
            config = jsonSimiCart['app-configs'][0]
            const app_settings = jsonSimiCart['app-configs'][0].app_settings
            if (
                config.themeitems &&
                config.api_version &&
                parseInt(config.api_version, 10)
            ) {
                if (this.state.isPhone) {
                    if (
                        app_settings.show_leftmenu_mobile &&
                        (parseInt(app_settings.show_leftmenu_mobile, 10) === 1) &&
                        config.themeitems.phone_left_menu_sections &&
                        config.themeitems.phone_left_menu_sections.length
                    ) {
                        leftMenuItems = config.themeitems.phone_left_menu_sections
                    }
                    if (
                        app_settings.show_bottommenu_mobile && 
                        (parseInt(app_settings.show_bottommenu_mobile, 10) === 1) && 
                        config.themeitems.phone_bottom_menu_items &&
                        config.themeitems.phone_bottom_menu_items.length
                    ) {
                        bottomMenuItems = config.themeitems.phone_bottom_menu_items
                    }
                    
                } else {
                    if (
                        app_settings.show_leftmenu_tablet &&
                        (parseInt(app_settings.show_leftmenu_tablet, 10) === 1) &&
                        config.themeitems.tablet_left_menu_sections &&
                        config.themeitems.tablet_left_menu_sections.length
                    ) {
                        leftMenuItems = config.themeitems.tablet_left_menu_sections
                    }
                    if (
                        app_settings.show_bottommenu_tablet && 
                        (parseInt(app_settings.show_bottommenu_tablet, 10) === 1) && 
                        config.themeitems.tablet_bottom_menu_items &&
                        config.themeitems.tablet_bottom_menu_items.length
                    ) {
                        bottomMenuItems = config.themeitems.tablet_bottom_menu_items
                    }
                }
            }
        }
        if (leftMenuItems || bottomMenuItems) 
            return (
                <Dashboardmenu 
                    className={className} 
                    classes={classes} 
                    leftMenuItems={leftMenuItems} 
                    rootCategoryId={rootCategoryId} 
                    bottomMenuItems={bottomMenuItems} 
                    config={config} 
                    history={this.props.history}
                    isPhone={this.state.isPhone}
                />
            )
    }

    render() {
        const {
            categoryTree,
            props,
            setRootNodeIdToParent
        } = this

        const {
            classes,
            closeDrawer,
            isOpen,
        } = props;
        const className = isOpen ? classes.root_open : classes.root;

        const simicartConfig = Identify.getAppDashboardConfigs()
        if (simicartConfig) {
            const dbMenu = this.renderDashboardMenu(className, simicartConfig)
            if (dbMenu)
                return dbMenu
        }
        
        return (
            <aside className={className}>
                <nav className={classes.body}>{categoryTree}</nav>
            </aside>
        )
    }
}

export default compose(
    withRouter,
    classify(defaultClasses)
)(Navigation);
