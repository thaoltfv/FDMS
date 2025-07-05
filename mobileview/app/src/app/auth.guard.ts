import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { NavController } from '@ionic/angular';
import { AuthService } from './auth.service';

export const authGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router = inject(Router);
  const navCtrl = inject(NavController);
  if (auth.isLoggedIn()) {
    return true;
  } else {
    navCtrl.navigateRoot(['/login']);
    return false;
  }
}; 