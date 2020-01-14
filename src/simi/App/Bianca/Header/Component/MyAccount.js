import React from 'react';
import User from 'src/simi/App/Bianca/BaseComponents/Icon/User';
import Identify from 'src/simi/Helper/Identify';
import MenuItem from '@material-ui/core/MenuItem';
import ClickAwayListener from '@material-ui/core/ClickAwayListener';
import Grow from '@material-ui/core/Grow';
import Popper from '@material-ui/core/Popper';
import { connect } from 'src/drivers';
import { withRouter } from 'react-router-dom';
import { compose } from 'redux';
import { logout as signOutApi } from 'src/simi/Model/Customer';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import {showToastMessage} from 'src/simi/Helper/Message';

class MyAccount extends React.Component {
	state = {
		open: false
	};

	handleLink(link) {
		this.props.history.push(link);
	}

	handleToggle = () => {
		this.setState((state) => ({ open: !state.open }));
	};

	handleClose = (event) => {
		if (this.anchorEl.contains(event.target)) {
			return;
		}

		this.setState({ open: false });
	};

	handleClickItem = (link) => {
		this.handleLink(link);
		this.handleToggle();
	};

	executeLogout = () => {
		// Hide menu
		this.handleToggle();
		// Call api logout from backend
		signOutApi(this.signOutCallback.bind(this), {});
		showFogLoading();
	};

	signOutCallback = (data) => {
		hideFogLoading();
		if (data.errors) {
			let errorMsg = '';
			if (data.errors.length) {
				data.errors.map((error) => {
					errorMsg += error.message;
				});
				showToastMessage(errorMsg);
			}
		} else {
			// Redirect to page logout pwa
			this.handleLink('/logout.html');
		}
	};

	renderMyAccount = () => {
		const { open } = this.state;
		const { classes } = this.props;
		return (
			<Popper open={open} anchorEl={this.anchorEl} placement="bottom-start" transition disablePortal>
				{({ TransitionProps }) => (
					<Grow {...TransitionProps} id="menu-list-grow" style={{ transformOrigin: 'center bottom' }}>
						<div className={classes['menu-my-account']}>
							<ClickAwayListener onClickAway={this.handleClose}>
								<div className={classes['list-menu-account']}>
									<MenuItem
										className={classes['my-account-item-1']}
										onClick={() => this.handleClickItem('/account.html')}
									>
										{Identify.__('My Account')}
									</MenuItem>
									<hr className={classes['hr-menu-account']} />
									<MenuItem
										className={classes['my-account-item-2']}
										onClick={() => this.executeLogout()}
									>
										{Identify.__('Logout')}
									</MenuItem>
								</div>
							</ClickAwayListener>
						</div>
					</Grow>
				)}
			</Popper>
		);
	};
	renderOption = () => {
		const { open } = this.state;
		const { classes } = this.props;
		return (
			<Popper open={open} anchorEl={this.anchorEl} placement="bottom-start" transition disablePortal>
				{({ TransitionProps }) => (
					<Grow {...TransitionProps} id="before-login-list-grow" style={{ transformOrigin: 'center bottom' }}>
						<div className={classes['before-login']}>
							<ClickAwayListener onClickAway={this.handleClose}>
								<div className={classes['list-before-login']}>
									<MenuItem
										className={classes['before-login-item-1']}
										onClick={() => this.handleClickItem('/login.html')}
									>
										{Identify.__('Login as Buyer')}
									</MenuItem>
									<hr className={classes['hr-before-login']} />
									<MenuItem
										className={classes['before-login-item-2']}
										onClick={() => this.handleClickItem('/designer_login.html')}
									>
										{Identify.__('Login as Designer')}
									</MenuItem>
								</div>
							</ClickAwayListener>
						</div>
					</Grow>
				)}
			</Popper>
		);
	};

	render() {
		const { props } = this;
		const { firstname, isSignedIn, classes } = props;
		const account = firstname ? <span className={classes['customer-firstname']}>{`Hi, ${firstname}`}</span> : null;
		return (
			<div
				style={{ position: 'relative' }}
				ref={(node) => {
					this.anchorEl = node;
				}}
			>
				<div role="presentation" onClick={this.handleToggle}>
					<div className={classes['item-icon']} style={{ display: 'flex', justifyContent: 'center' }}>
						<User />
					</div>
					{account}
					{/* <div className={classes["item-text"]} style={{whiteSpace : 'nowrap'}}>
                        {account}
                    </div> */}
				</div>
				{!isSignedIn && this.renderOption()}
				{isSignedIn && this.renderMyAccount()}
			</div>
		);
	}
}

const mapStateToProps = ({ user }) => {
	const { currentUser, isSignedIn } = user;
	const { firstname, lastname } = currentUser;

	return {
		firstname,
		isSignedIn,
		lastname
	};
};

export default compose(connect(mapStateToProps), withRouter)(MyAccount);
