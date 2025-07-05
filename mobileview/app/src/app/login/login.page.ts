import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { 
  IonHeader, 
  IonToolbar, 
  IonTitle, 
  IonContent, 
  IonButton, 
  IonInput, 
  IonItem, 
  IonLabel, 
  IonList,
  IonIcon,
  IonSpinner
} from '@ionic/angular/standalone';
import { AuthService } from '../auth.service';
import { addIcons } from 'ionicons';
import { 
  personCircleOutline,
  documentTextOutline,
  mailOutline,
  lockClosedOutline,
  alertCircleOutline
} from 'ionicons/icons';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    CommonModule, 
    FormsModule, 
    IonHeader, 
    IonToolbar, 
    IonTitle, 
    IonContent, 
    IonButton, 
    IonInput, 
    IonItem, 
    IonLabel, 
    IonList,
    IonIcon,
    IonSpinner
  ],
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss']
})
export class LoginPage {
  email = '';
  password = '';
  loading = false;
  error = '';

  constructor(private auth: AuthService, private router: Router, private toastCtrl: ToastController) {
    addIcons({ 
      'person-circle-outline': personCircleOutline,
      'document-text-outline': documentTextOutline,
      'mail-outline': mailOutline,
      'lock-closed-outline': lockClosedOutline,
      'alert-circle-outline': alertCircleOutline
    });
  }

  async onLogin() {
    this.loading = true;
    this.error = '';
    this.auth.login(this.email, this.password).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/home'], { replaceUrl: true });
      },
      error: async (err) => {
        this.loading = false;
        this.error = err?.error?.message || 'Login failed';
        const toast = await this.toastCtrl.create({
          message: this.error,
          duration: 2000,
          color: 'danger'
        });
        toast.present();
      }
    });
  }
} 