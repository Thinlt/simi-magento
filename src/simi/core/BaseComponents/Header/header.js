import React, { Suspense } from 'react'
import Identify from "src/simi/Helper/Identify";
import WishList from 'src/simi/core/BaseComponents/Icon/WishList'
import MenuIcon from 'src/simi/core/BaseComponents/Icon/Menu'
import NavTrigger from './navTrigger'
import CartTrigger from './cartTrigger'
import defaultClasses from './header.css'
import { mergeClasses } from 'src/classify'
import { Link, resourceUrl, Route } from 'src/drivers';

const $ = window.$ 

const SearchForm = React.lazy(() => import('./Component/SearchForm'));

class Header extends React.Component{
    constructor(props) {
        super(props);
        this._mounted = true;
        const isPhone = window.innerWidth < 768 ;
        this.state = {isPhone}
        this.classes = mergeClasses(defaultClasses, this.props.classes)
    }

    setIsPhone(){
        const obj = this;
        $(window).resize(function () {
            const width = window.innerWidth;
            const isPhone = width < 768;
            if(obj.state.isPhone !== isPhone){
                obj.setState({isPhone})
            }
        })
    }

    componentDidMount(){
        this.setIsPhone();
    }

    renderLogo = () => {
        const {isPhone} = this.state;
        return (
            <div className={`${this.classes['search-icon']} ${this.classes['header-logo']}`} >
                <Link to={resourceUrl('/')}>
                    <img 
                        src="https://www.simicart.com/skin/frontend/default/simicart2.0/images/simicart/new_logo_small.png" 
                        alt="harlow-logo" style={!isPhone?{width: 206, height: 48}:{width: 135, height: 32}}/>
                </Link>
            </div>
        )
    }

    renderSearhForm = () => {
        return(
            <div className={this.classes['header-search']}>
                <Suspense fallback={null}>
                    <Route
                        render={({ history, location }) => (
                            <SearchForm
                                history={history}
                                location={location}
                            />
                        )}
                    />
                </Suspense>
            </div>
        )
    }

    renderRightBar = () => {
        return(
            <div className={this.classes['right-bar']}>
                <div className={this.classes['right-bar-item']} id="my-account">
                    
                </div>
                <div 
                    className={this.classes['right-bar-item']} id="wish-list" 
                >
                    <Link to={resourceUrl('/wishlist')}>
                        <div className={this.classes['item-icon']} style={{display: 'flex', justifyContent: 'center'}}>
                            <WishList style={{width: 30, height: 30, display: 'block'}} />
                        </div>
                        <div className={this.classes['item-text']}>
                            {Identify.__('Favourites')}
                        </div>
                    </Link>
                </div>
                <div className={this.classes['right-bar-item']}>
                    <CartTrigger classes={this.classes}/>
                </div>
            </div>
        )
    }

    renderViewPhone = () => {
        return(
            <div>
                <div className="container">
                    <div className={this.classes['header-app-bar']}>
                        <NavTrigger>
                            <MenuIcon color="#333132" style={{width:30,height:30}}/>
                        </NavTrigger>
                        {this.renderLogo()}
                        <div className={this.classes['right-bar']}>
                            <div className={this.classes['right-bar-item']}>
                                <CartTrigger />
                            </div>
                        </div>
                    </div>
                </div>
                {this.renderSearhForm()}
            </div>


        )
    }

    render(){
        this.classes = mergeClasses(defaultClasses, this.props.classes);
        if(window.innerWidth < 1024){
            return this.renderViewPhone()
        }
        return(
            <div className="container">
                <div className={this.classes['header-app-bar']}>
                    {this.renderLogo()}
                    {this.renderSearhForm()}
                    {this.renderRightBar()}
                </div>
            </div>

        )
    }
}
export default Header