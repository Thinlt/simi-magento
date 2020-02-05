import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import ListItemNested from 'src/simi/App/Bianca/BaseComponents/MuiListItem/Nested';
import MenuItem from 'src/simi/App/Bianca/BaseComponents/MenuItem';
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
        const sortedCats = allCats.children.sort((a,b)=>{
            return a.position - b.position
        })
        menuRender = sortedCats.map(item => {
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
        if (storeConfig && storeConfig.simiRootCate) {
            const item = this.renderTreeItem(storeConfig.simiRootCate);
            return item;
        }
        return '';
    }
}

export default CateTree;
