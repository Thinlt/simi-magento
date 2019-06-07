/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 12/7/18
 * Time: 2:38 PM
 */
import React from 'react'
import Abstract from '../../../Core/BaseAbstract'
import User from "../../Icon/User";
import Identify from "../../../../Helper/Identify";
import Customer from "../../../../Helper/Customer";
import MenuItem from '@material-ui/core/MenuItem'
import ClickAwayListener from '@material-ui/core/ClickAwayListener';
import Grow from '@material-ui/core/Grow';
import Popper from '@material-ui/core/Popper';
class MyAccount extends Abstract{

    state = {
        open: false,
    };

    handleToggle = () => {
        this.setState(state => ({ open: !state.open }));
    };

    handleClose = event => {
        if (this.anchorEl.contains(event.target)) {
            return;
        }

        this.setState({ open: false });
    };

    handleMenuAccount = link => {
        this.handleLink(link)
        this.handleToggle()
    }

    renderMyAccount = ()=>{
        const { open } = this.state;
        return (
            <Popper open={open} anchorEl={this.anchorEl}
                    placement="bottom-start"
                    transition disablePortal>
                {({ TransitionProps }) => (
                    <Grow
                        {...TransitionProps}
                        id="menu-list-grow"
                        style={{ transformOrigin:'center bottom' }}
                    >

                            <div className="menu-my-account">
                                <ClickAwayListener onClickAway={this.handleClose}>
                                <div className="list-menu-account">
                                    <MenuItem className="my-account-item" onClick={()=>this.handleMenuAccount('/customer/account')}>
                                        {Identify.__('My Account')}
                                    </MenuItem>
                                    <MenuItem className="my-account-item" onClick={()=>this.handleMenuAccount('/customer/account/logout')}>
                                        {Identify.__('Logout')}
                                    </MenuItem>
                                </div>
                                </ClickAwayListener>
                            </div>
                    </Grow>
                )}
            </Popper>
        )
    }

    handleClickAccount = () =>{
        if(Customer.isLogin()){
            this.handleToggle()
        }else{
            this.handleLink('/customer/account/login')
        }
    }

    render() {
        let customer = Customer.getCustomerData() || {}
        let account = !!customer.firstname ? <span className="customer-firstname" style={{color:'#0F7D37'}}>{`Hi, ${customer.firstname}`}</span>: Identify.__('Account')
        return (
            <div style={{position:'relative'}}  ref={node => {
                this.anchorEl = node;
            }}>
                <div onClick={this.handleClickAccount}>
                    <div className="item-icon" style={{display: 'flex', justifyContent: 'center'}}>
                        <User style={{width: 30, height: 30, display: 'block'}} />
                    </div>
                    <div className="item-text" style={{whiteSpace : 'nowrap'}}>
                        {account}
                    </div>
                </div>
                {Customer.isLogin() && this.renderMyAccount()}
            </div>
        );
    }
}
export default MyAccount