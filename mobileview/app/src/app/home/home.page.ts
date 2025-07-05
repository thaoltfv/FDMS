import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { 
  IonHeader, 
  IonToolbar, 
  IonTitle, 
  IonContent, 
  IonButton, 
  IonButtons,
  IonAvatar,
  IonIcon,
  IonCard,
  IonCardHeader,
  IonCardTitle,
  IonCardSubtitle,
  IonCardContent,
  IonList,
  IonItem,
  IonLabel,
  IonChip,
  IonGrid,
  IonRow,
  IonCol,
  IonBadge,
  NavController
} from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import { 
  personCircleOutline, 
  documentTextOutline, 
  folderOutline, 
  addOutline, 
  searchOutline,
  timeOutline,
  checkmarkCircleOutline,
  createOutline,
  chevronForwardOutline,
  checkmarkCircle,
  create
} from 'ionicons/icons';
import { AuthService } from '../auth.service';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  imports: [
    CommonModule,
    IonHeader, 
    IonToolbar, 
    IonTitle, 
    IonContent, 
    IonButton, 
    IonButtons,
    IonAvatar,
    IonIcon,
    IonCard,
    IonCardHeader,
    IonCardTitle,
    IonCardSubtitle,
    IonCardContent,
    IonList,
    IonItem,
    IonLabel,
    IonChip,
    IonGrid,
    IonRow,
    IonCol,
    IonBadge
  ],
})
export class HomePage implements OnInit {
  // Mock data for interested documents
  interestedDocuments = [
    {
      id: '1',
      title: 'QSDĐ và CTXD tại Phường Thới Hòa, thành ...',
      category: 'Khảo sát hiện trạng nhà đất',
      updatedAt: '2 giờ trước',
      status: 'active'
    },
    {
      id: '2',
      title: 'QSDĐ tại đường ĐT 755, thôn 8, xã Thống Nhất...',
      category: 'Khảo sát hiện trạng nhà đất',
      updatedAt: '1 ngày trước',
      status: 'active'
    }
  ];

  // Mock data for document collections (blueprints)
  documentCollections = [
    {
      id: '1',
      name: 'Khảo sát hiện trạng nhà đất',
      documentCount: 25,
      lastUpdated: 'Cập nhật gần đây',
      icon: 'folder-outline'
    }
  ];

  // Mock data for recent activities
  recentActivities = [
    {
      id: '1',
      user: 'Hoàng Anh Tuấn',
      action: 'đã gửi tài liệu',
      time: '30 phút trước',
      type: 'success'
    },
    {
      id: '2',
      user: 'Lê Thị Thu',
      action: 'đã lưu nháp',
      time: '2 giờ trước',
      type: 'warning'
    }
  ];

  constructor(private authService: AuthService, private navCtrl: NavController) {
    // Add icons to ionicons registry
    addIcons({
      'person-circle-outline': personCircleOutline,
      'document-text-outline': documentTextOutline,
      'folder-outline': folderOutline,
      'add-outline': addOutline,
      'search-outline': searchOutline,
      'time-outline': timeOutline,
      'checkmark-circle-outline': checkmarkCircleOutline,
      'create-outline': createOutline,
      'chevron-forward-outline': chevronForwardOutline,
      'checkmark-circle': checkmarkCircle,
      'create': create
    });
  }

  ngOnInit() {
    // TODO: Load real data from services
    this.loadInterestedDocuments();
    this.loadDocumentCollections();
    this.loadRecentActivities();
  }

  // Methods for handling user interactions
  onDocumentClick(documentId: string) {
    console.log('Document clicked:', documentId);
    // TODO: Navigate to document detail page
  }

  onCollectionClick(collectionId: string) {
    console.log('Collection clicked:', collectionId);
    // TODO: Navigate to collection page
  }

  onCreateNewDocument() {
    console.log('Create new document clicked');
    // TODO: Navigate to create document page
  }

  onSearchClick() {
    console.log('Search clicked');
    // TODO: Navigate to search page
  }

  onActivityClick(activityId: string) {
    console.log('Activity clicked:', activityId);
    // TODO: Navigate to activity detail
  }

  navigateToProfile() {
    console.log('Navigate to profile clicked');
    this.navCtrl.navigateForward(['/profile']);
  }

  // Mock methods for loading data (replace with actual service calls)
  private loadInterestedDocuments() {
    // TODO: Replace with actual API call
    console.log('Loading interested documents...');
  }

  private loadDocumentCollections() {
    // TODO: Replace with actual API call
    console.log('Loading document collections...');
  }

  private loadRecentActivities() {
    // TODO: Replace with actual API call
    console.log('Loading recent activities...');
  }

  // Helper method to get activity status color
  getActivityStatusColor(type: string): string {
    switch (type) {
      case 'success': return 'success';
      case 'warning': return 'warning';
      case 'error': return 'danger';
      default: return 'medium';
    }
  }
}
