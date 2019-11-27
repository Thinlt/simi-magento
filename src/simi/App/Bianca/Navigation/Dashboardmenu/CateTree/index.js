import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import { cateUrlSuffix } from 'src/simi/Helper/Url';
import { configColor } from 'src/simi/Config';
import CateIcon from 'src/simi/BaseComponents/Icon/TapitaIcons/List';
import ListItemNested from 'src/simi/App/Bianca/BaseComponents/MuiListItem/Nested';
import MenuItem from 'src/simi/App/Bianca/BaseComponents/MenuItem';
import SubCate from './Subcate';
require('./index.scss');

class CateTree extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            treecate: null,
            loaded: false,
            open: false
        };
    }

    shouldComponentUpdate() {
        return !this.renderedOnce;
    }

    openLocation = location => {
        this.props.handleMenuItem(location);
    };

    renderTitleMenu = title => {
        const classes = this.props;
        return (
            <div className={classes['menu-cate-name-item']}>
                {Identify.__(title)}
            </div>
        );
    };

    renderTreeItem(allCats) {
        // console.log(Object.keys(allCats).length)
        if (Object.keys(allCats).length > 1) {
            return (
                <div className="left-cats-menu">
                    <ListItemNested
                        primarytext={
                            <div className="left-all-cats">
                                {Identify.__('ALL CATEGORIES')}
                            </div>
                        }
                    >
                        {this.renderSubItem(allCats)}
                    </ListItemNested>
                </div>
            );
        }

        return false;
    }

    renderSubItem(allCats) {
        let menuRender = [];
        menuRender = allCats.children.map(item => {
            const catesItem = {
                ...item,
                url: `/${item.url_path}.html`
            }

            if(!item.name){
                return null
            }

            const catsItem = (
                <div
                    className={'list-cats-menu-item'}
                    style={{ display: 'flex' }}
                >
                    <div className={`cate-item-name`}>{item.name}</div>
                </div>
            );
            return (
                <div
                    role="presentation"
                    key={Identify.randomString(5)}
                    style={{ marginLeft: 5, marginRight: 5 }}
                    onClick={() => this.openLocation(catesItem)}
                >
                    <MenuItem title={catsItem} className="left-cats-item" />
                </div>
            );
        }, this);

        return menuRender;
    }

    renderMenuItem = (cate_name, location) => {
        return (
            <div
                role="presentation"
                key={Identify.randomString(10)}
                style={{ textTransform: 'uppercase' }}
                onClick={() => this.openLocation(location)}
                className="cate-child-item"
            >
                <div>{cate_name}</div>
            </div>
        );
    };

    handleToggleMenu = id => {
        const { classes } = this.props;
        const cate = $('.cate-' + id);
        $('.sub-cate-' + id).slideToggle('fast');
        cate.find(`.${classes['cate-icon']}`).toggleClass('hidden');
    };

    renderJs = () => {
        $(function() {
            if (Identify.isRtl()) {
                $('div.menu-cate-name-item').each(function() {
                    const parent = $(this).parent();
                    const margin = parent.css('margin-left');
                    parent.css({
                        'margin-left': 0,
                        'margin-right': margin
                    });
                });
            }
        });
    };

    render() {
        const storeConfig = Identify.getStoreConfig();
        try {
            const item = this.renderTreeItem(storeConfig.simiRootCate);
            return item;
        } catch (err) {
            console.log(err);
        }
        return '';
    }
}

export default CateTree;
