import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { IonHeader, IonToolbar, IonTitle, IonContent, IonButton, IonInput, IonItem, IonLabel, IonList } from '@ionic/angular/standalone';
import { NavController } from '@ionic/angular';
import { AuthService } from '../auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, IonHeader, IonToolbar, IonTitle, IonContent, IonButton, IonInput, IonItem, IonLabel, IonList],
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss']
})
export class RegisterPage {
  email = '';
  password = '';
  full_name = '';
  loading = false;
  error = '';

  constructor(private auth: AuthService, private router: Router, private toastCtrl: ToastController, private navCtrl: NavController) {}

  async onRegister() {
    this.loading = true;
    this.error = '';
    this.auth.register(this.email, this.password, this.full_name).subscribe({
      next: async (res) => {
        this.loading = false;
        const toast = await this.toastCtrl.create({
          message: 'Registration successful! Please login.',
          duration: 2000,
          color: 'success'
        });
        toast.present();
        this.navCtrl.navigateRoot(['/login']);
      },
      error: async (err) => {
        this.loading = false;
        this.error = err?.error?.message || 'Registration failed';
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